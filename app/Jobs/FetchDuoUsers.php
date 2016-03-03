<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class FetchDuoUsers
 * @package App\Jobs
 */
class FetchDuoUsers extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

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

        //Query Duo REST API for Users (all)
        $response = $duoAdmin->users();

        //Duo SDK puts results in nested array response[response]
        $users = $response['response']['response'];

        //Loop the users
        foreach($users as $user)
        {
            //Get an existing Duo User or create a new one
            $duoUser = \App\Models\DuoUser::firstOrCreate([
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
            $duoUser->save();

            $userGroupList = [];

            //Loop Duo User Groups
            foreach($user['groups'] as $group)
            {
                $localGroup = \App\Models\DuoGroup::where('group_id',$group['group_id'])->first();
                $userGroupList[] = $localGroup->id;

            }
            $duoUser->duoGroups()->sync($userGroupList);

            unset($userGroupList);


            //Create array to hold list of users phones
            $userPhoneList = [];

            foreach($user['phones'] as $phone)
            {
                //Get an existing Duo Phone or create a new one
                $localPhone = \App\Models\DuoPhone::firstOrCreate([
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
                    $cap = \App\Models\DuoCapability::where('name',$capability)->first();

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

        dd('done');
//        \Log::debug('All Users:', [$user]);
    }
}
