<?php

namespace App\Jobs;


use App\Jobs\Job;
use App\Models\Duo\Group;
use App\Models\Duo\Phone;
use App\Models\Duo\Token;
use App\Libraries\DuoAdmin;
use App\Models\Duo\Capability;
use App\Models\Duo\User as User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;


/**
 * Class FetchDuoUsers
 * @package App\Jobs
 */
class FetchDuoUsers extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;

    /**
     * @var
     */
    private $users;
    /**
     * @var string
     */
    private $username;


    /**
     * @param null $username
     */
    public function __construct($username = NULL)
    {
        $this->username = $username;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        \Log::debug('Set PHP time limit to zero (no limit) and memory to 2GB');


        //Create the Duo Admin Client and set the timeout higher than default
        $duoAdmin = new DuoAdmin();
        $duoAdmin->setRequesterOption('timeout','6000000');
        \Log::debug('Created new DuoAdmin object', [$duoAdmin]);

        //Query Duo REST API
        $response = $duoAdmin->users($this->username);

        //Duo SDK puts results in nested array [response][response]
        $this->users = $response['response']['response'];
        \Log::debug('Obtained User(s) from Duo API - ', [count($this->users)]);

        //Remove local Duo accounts that no longer exist in the Duo online database
        $this->removeStaleAccounts($this->users);
        \Log::debug('Finished removeStaleAccounts function');

        //If we only queried for one user
        //there's just one user to process
        \Log::debug('Begin extractUserData function');
        if(!isset($this->users[0]))
        {
            //Begin main process for looping Duo User Data
            $this->extractUserData($this->users);

        } else {
            //Loop the array of users
            foreach($this->users as $user)
            {
                //Begin main process for looping Duo User Data
                $this->extractUserData($user);
            }
        }
        \Log::debug('Completed FetchDuoUsers Job');
    }

    /**
     * @param $user
     */
    private function extractUserData($user)
    {
        \Log::debug('Extracting Data for user - ', [$user]);

        // Get an existing Duo User or create a new one
        $duoUser = User::where(['user_id' => $user['user_id']])->withTrashed()->first() ?: new User(['user_id' => $user['user_id']]);
        // If this user was somehow previously trashed then restore the account
        if ($duoUser->trashed()) $duoUser->restore();
        \Log::debug('Local DuoUser found or created - ', [$duoUser]);

        //Update Duo User specific fields
        $duoUser->username = $user['username'];
        $duoUser->email = $user['email'];
        $duoUser->status = $user['status'];
        $duoUser->realname = $user['realname'];
        $duoUser->notes = $user['notes'];
        $duoUser->last_login = $user['last_login'];

        //Save Duo User
        $duoUser->touch();
        $duoUser->save();

        \Log::debug('Local DuoUser saved - ', [$duoUser]);

        if(count($user['tokens']) > 0)
        {
            \Log::debug('This user has tokens registered - ', [$user['tokens']]);
            //Loop Duo User Desktop Tokens
            $tokenList = [];
            foreach($user['tokens'] as $token)
            {

                $localToken = Token::firstOrCreate([
                    'serial' => $token['serial'],
                    'token_id' => $token['token_id'],
                    'totp_step' => $token['totp_step'],
                    'type' => $token['type'],
                ]);

                $tokenList[] = $localToken->id;

            }
            $duoUser->duoTokens()->sync($tokenList);

            \Log::debug('Finished Processing User tokens');
            unset($tokenList);
        }

        //Get the Duo groups that the user is assigned to
        //in UC Insight for reporting purposes
        $userGroupList = $duoUser->duoGroups()->wherePivot('duo_assigned',false)->lists('id')->toArray();
        \Log::debug('Syncing Duo User groups ', [$userGroupList]);

        //Loop Duo-assigned User Groups
        foreach($user['groups'] as $group)
        {
            //Get the ID of the group that was created
            //locally during the Duo Group sync
            $localGroup = Group::where('group_id',$group['group_id'])->first();
            //Set the duo_assigned attribute to true
            //since we got this pairing from the Duo API
            $userGroupList[$localGroup->id] = ['duo_assigned' => true];
        }
        $duoUser->duoGroups()->sync($userGroupList);
        \Log::debug('Finished Processing User Groups');

        unset($userGroupList);

        //Hand off to process the Duo User phone data
        $this->extractUserPhoneData($duoUser,$user);
        \Log::debug('Completed User data extraction process', [$duoUser]);


    }

    /**
     * @param User $duoUser
     * @param $user
     */
    private function extractUserPhoneData(User $duoUser, $user)
    {
        \Log::debug('Processing User Phones', [$user['phones']]);

        //Create array to hold list of users phones
        $userPhoneList = [];

        foreach($user['phones'] as $phone)
        {
            //Get an existing Duo Phone or create a new one
            $localPhone = Phone::firstOrCreate([
                'phone_id' => $phone['phone_id'],

            ]);

            //Populate Duo Phone fields
            $localPhone->name = $phone['name'];
            $localPhone->number = $phone['number'];
            $localPhone->extension = $phone['extension'];
            $localPhone->type = $phone['type'];
            $localPhone->platform = $phone['platform'];
            $localPhone->postdelay = $phone['postdelay'];
            $localPhone->predelay = $phone['predelay'];
            $localPhone->sms_passcodes_sent = $phone['sms_passcodes_sent'];
            $localPhone->actived = $phone['activated'];

            //Save the Duo Phone
            $localPhone->save();

            //Push the Duo Phone ID onto the array
            $userPhoneList[] = $localPhone->id;

            //Create an array to hold the phones capabilities
            $phoneCapabilityList = [];

            //Loop through the phones assigned capabilities
            foreach($phone['capabilities'] as $capability)
            {
                $cap = Capability::where('name',$capability)->first();

                if($cap) {
                    //Populate the array of capabilities
                    $phoneCapabilityList[] = $cap->id;
                }
            }

            //Sync the phones capabilities
            $localPhone->duoCapabilities()->sync($phoneCapabilityList);

            unset($phoneCapabilityList);
        }

        //Sync the Users Duo Phones
        $duoUser->duoPhones()->sync($userPhoneList);
        \Log::debug('Finished Processing User Phones');

    }

    /**
     * @param $freshDuoUserList
     */
    private function removeStaleAccounts($freshDuoUserList) {
        
        \Log::debug('Removing stale accounts from UC Insight (soft delete)');
        //Move the fresh list of usernames from Duo API to an array
        $newDuoUsers = [];
        foreach($freshDuoUserList as $user) {
            $newDuoUsers[] = $user['user_id'];
        }

        // If there's only 1 user, we're just doing an on-demand
        // user sync, so we shouldn't worry about stale accounts.
        if(count($newDuoUsers) == 1) {
            \Log::debug('We are syncing a single user, no need to remove stale accounts.  Exiting function.');
            return;
        }

        //Get a list of local Duo usernames
        $localDuoUsers = User::lists('user_id')->toArray();
        \Log::debug('Got a list of local DuoUsers - ', [count($localDuoUsers)]);

        //Compare the two arrays
        $staleUsers = array_diff($localDuoUsers,$newDuoUsers);
        \Log::debug('Local DuoUser stale accounts - ', [count($staleUsers)]);

        //Remove the 'stale' user accounts from the local database
        foreach($staleUsers as $dead) {
            \Log::debug('Removing stale local DuoUser account - ', [$dead]);
            User::where('user_id', $dead)->delete();
        }
    }
}
