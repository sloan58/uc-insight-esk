<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Duo\Group;
use App\Models\Duo\Phone;
use App\Models\Duo\Token;
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
    private $realname;

    /**
     * @var bool
     */
    private $user_id;

    /**
     * @param string $realname
     * @param bool $user_id
     */
    public function __construct($realname = NULL, $user_id = FALSE)
    {
        $this->realname = $realname;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);

        //Create Duo Admin Client
        $duoAdmin = new \DuoAPI\Admin(env('DUO_IKEY'),env('DUO_SKEY'),env('DUO_HOST'));

        //Query Duo REST API
        $response = $duoAdmin->users($this->realname,$this->user_id);

        //Duo SDK puts results in nested array [response][response]
        $this->users = $response['response']['response'];

        //If we only queried for one user
        //there's just one user to process
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
    }

    /**
     * @param $user
     */
    private function extractUserData($user)
    {
        //Get an existing Duo User or create a new one
        $duoUser = User::firstOrCreate([
            'user_id' => $user['user_id']
        ]);

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

        if(count($user['tokens']) > 0)
        {
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

            unset($tokenList);
        }

        //Get the Duo groups that the user is assigned to
        //in UC Insight for reporting purposes
        $userGroupList = $duoUser->duoGroups()->wherePivot('duo_assigned',false)->lists('id')->toArray();

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

        unset($userGroupList);

        //Hand off to process the Duo User phone data
        $this->extractUserPhoneData($duoUser,$user);


    }

    /**
     * @param User $duoUser
     * @param $user
     */
    private function extractUserPhoneData(User $duoUser, $user)
    {
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

                //Populate the array of capabilities
                $phoneCapabilityList[] = $cap->id;
            }

            //Sync the phones capabilities
            $localPhone->duoCapabilities()->sync($phoneCapabilityList);

            unset($phoneCapabilityList);
        }

        //Sync the Users Duo Phones
        $duoUser->duoPhones()->sync($userPhoneList);
    }
}
