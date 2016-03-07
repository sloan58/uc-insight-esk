<?php

namespace App\Http\Controllers;

use App\Jobs\FetchDuoGroups;
use App\Jobs\FetchDuoUsers;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DuoController extends Controller
{

    function __construct()
    {
        //PROD
        $ikey = 'DI3J8XTOYNB2QJCJWN7X';
        $skey = 'toPflChBRV2ds4rtFGUHWwYr6ufG5Czzun6HENTm';
        $host = 'api-eba973a2.duosecurity.com';

        //Dev
//        $ikey = 'DI862RPCBG75K3SPHNMM';
//        $skey = '83tknn36V4XjDuCw11kbkji5tFakyOfpEhizuNen';
//        $host = 'api-d206e387.duosecurity.com';

        $this->duoAdmin = new \DuoAPI\Admin($ikey,$skey,$host);

        $this->duoAuth = new \DuoAPI\Auth($ikey,$skey,$host);
    }

    public function getPing()
    {
        return($this->duoAuth->ping());
    }

    public function getLogs()
    {

        $response = $this->duoAdmin->authLogs();

        $logs = $response['response']['response'];

        foreach($logs as $log)
        {
            if($log['result'] == 'FAILURE')
            {
                print $log['username'] . " failed login from device " . $log['device'] . " at " . date('c',$log['timestamp']) . " for reason " . $log['reason'] . "\n";
            }
        }

    }

    public function getUsers()
    {

        $this->dispatch(new FetchDuoUsers());

        return response('Job dispatched');

    }

    public function getUser()
    {
        set_time_limit(0);


        //PROD
        $ikey = 'DI3J8XTOYNB2QJCJWN7X';
        $skey = 'toPflChBRV2ds4rtFGUHWwYr6ufG5Czzun6HENTm';
        $host = 'api-eba973a2.duosecurity.com';

        $duoAdmin = new \DuoAPI\Admin($ikey,$skey,$host);

        $response = $duoAdmin->users('Aaron Dhiman');

        $users = $response['response']['response'];

        dd($users);

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
    }

    public function getGroups()
    {
        $this->dispatch(new FetchDuoGroups());

        return response('Job dispatched');
    }

}
