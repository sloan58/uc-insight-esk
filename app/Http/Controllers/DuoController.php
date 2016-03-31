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
                ->paginate(10)
            ;
        } else {
            $users = DB::table('duo_users')->paginate(10);
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
        $user = DuoUser::find($id);

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

        $user = DuoUser::find($id);

        $this->dispatch(new FetchDuoUsers($user));

        alert()->success("Duo User Sync for " . $user->realname . " submitted successfully!");
        return redirect()->back();
    }
}
