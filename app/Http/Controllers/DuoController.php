<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Report;
use App\Http\Requests;
use App\Models\Duo\Group;
use App\Jobs\FetchDuoUsers;
use Illuminate\Http\Request;
use App\Models\Duo\User as DuoUser;
use App\Jobs\GenerateRegisteredDuoUsersReport;
use App\Console\Commands\GenerateRegisteredDuoUsersReportCommand;

/**
 * Class DuoController
 * @package App\Http\Controllers
 */
class DuoController extends Controller
{
    /**
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        if($request->input('search'))
        {
            $search = $request->get('search');

            $users = DuoUser::where('realname', 'like', "%$search%")
                ->orWhere('username', 'like', "%$search%")
                ->orderBy('username','asc')
                ->paginate(10)
            ;
        } else {
            $users = DB::table('duo_users')->orderBy('username','asc')->paginate(10);
        }

        return view('duo.index', compact('users'));
    }

    /**
     * @param $id
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function showUser($id)
    {
        $user = DuoUser::find($id);

        $reports = Report::where('name','like','%duo%')->get();
        $groups = Group::all();

        return view('duo.show',compact('user','reports','groups'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        //Get the report ID's from the form
        $reports = $request->reports;

        //Get the user we're working with from $id
        $user = DuoUser::find($id);

        //If there are no reports, detach all
        if(is_null($reports))
        {
            $user->reports()->detach();
        } else {
            //There were reports, let's sync them.
            $user->reports()->sync($request->reports);
        }

        alert()->success("Duo User " . $user->realname . " updated successfully!");
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUserGroups(Request $request, $id)
    {
        //Get the Duo User from the URL $id
        $user = DuoUser::findOrFail($id);

        //Get the Duo Groups submitted for association
        $groups = $request->groups;

        //Get the current Duo Assigned groups for this user
        $duoAssignedGroups = $user->duoGroups()->where('duo_assigned',true)->lists('id')->toArray();

        //Set the duo_assigned pivot values to true
        //on groups assigned in Duo
        foreach($duoAssignedGroups as $assignedGroup)
        {
            //See if $groups contains any data
            if(isset($groups))
            {
                //Check if the Duo Assigned Group is in the array
                if(in_array($assignedGroup,$groups))
                {
                    //Get the key for the array value
                    $key = array_search($assignedGroup,$groups);
                    //Unset the key, we'll update it below with the pivot value
                    unset($groups[$key]);
                }
            }
            //Set the duo_assigned parameter to true
            $groups[$assignedGroup] = ['duo_assigned' => true];
        }

        //Sync the users groups
        $user->duoGroups()->sync($groups);

        alert()->success("Duo User " . $user->realname . " updated successfully!");
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onDemandGroupReport($id)
    {
        //Get the Local Duo User
        $user = DuoUser::find($id);

        //Send the $user off to generate a report
        $this->dispatch(new GenerateRegisteredDuoUsersReport($user));

        alert()->success("Duo User Report for " . $user->realname . " submitted successfully!");
        return redirect()->back();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function onDemandUserSync($id)
    {
        //Get the Local Duo User
        $user = DuoUser::find($id);

        //Send the Real Name off to sync with Duo API
        $this->dispatch(new FetchDuoUsers($user->realname,$user->user_id));

        alert()->success("Duo User Sync for " . $user->realname . " submitted successfully!");
        return redirect()->back();
    }

    public function migrateUser($id)
    {
        //Create Duo Admin Client
        $duoAdmin = new \DuoAPI\Admin(env('DUO_IKEY'),env('DUO_SKEY'),env('DUO_HOST'));

        //Get the local Duo User account ID
        $insightUser = \App\Models\Duo\User::findorFail($id);

        //Fetch the User details via Duo API
        $res = $duoAdmin->users($insightUser->username);

        //If we didn't get the user object back, error and redirect
        if(!count($res['response']['response']))
        {
            \Log::debug('Duo User not found for migrate function', [$insightUser]);
            alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin");
            return redirect('duo/user/' . $id);
        }

        //Grab the user details
        $user = $res['response']['response'][0];

        //Implode the explode...  (Remove the space from the username)
        $user['username'] = implode('', explode(' ', $user['username']));

        //Create the new Duo User
        $res = $duoAdmin->create_user($user['username'],$user['realname'],$user['email'],$user['status'],$user['notes']);

        //If the status is not OK, error and redirect
        if($res['response']['stat'] != "OK")
        {
            \Log::debug('Error while creating new Duo User', [$insightUser,$user,$res]);
            alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin");
            return redirect('duo/user/' . $id);
        }

        //Our 'Add Duo User' call was successful.
        //Assign the new user to this variable
        $newDuoUser = $res['response']['response'];

        //Sync Phones to new Duo User account
        foreach($insightUser->duoPhones()->lists('phone_id')->toArray() as $phone)
        {
            $res = $duoAdmin->user_associate_phone($newDuoUser['user_id'],$phone);

            \Log::debug('Associate Phone Res:', [$res]);
        }

        //Sync Tokens to new Duo User account
        foreach($insightUser->duoTokens()->lists('token_id')->toArray() as $token)
        {
            $res = $duoAdmin->user_associate_token($newDuoUser['user_id'],$token);
            //If the status is not OK, error and redirect
            if(!$res['response']['stat'] != "OK")
            {
                \Log::debug('Error Associating Token Res:', [$res]);
            }
        }

        //Sync the new Duo User with UC Insight via Duo API
        $this->dispatch(new FetchDuoUsers($newDuoUser['realname'],$newDuoUser['user_id']));

        alert()->success("Duo User Migration for " . $newDuoUser['realname'] . " processed successfully!");
        return redirect('duo');

    }

    public function registeredUsersReport()
    {
        //Get the users that have a phone or token
        $phoneUsers = \App\Models\Duo\User::has('duoPhones')->get();
        $tokenUsers = \App\Models\Duo\User::has('duoTokens')->get();

        $users = $phoneUsers->merge($tokenUsers);

        return view('duo.registered-report',compact('users'));
    }
}
