<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use App\Models\Report;
use App\Http\Requests;
use App\Models\Duo\Log;
use App\Models\Duo\Group;
use App\Libraries\DuoAdmin;
use App\Jobs\FetchDuoUsers;
use Illuminate\Http\Request;
use App\Models\Duo\User as DuoUser;
use App\Jobs\GenerateRegisteredDuoUsersReport;
use App\Console\Commands\GenerateRegisteredDuoUsersReportCommand;
use Keboola\Csv\CsvFile;
use Yajra\Datatables\Datatables;

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

        alert()->success("Duo User " . $user->realname . " updated successfully!")->autoclose(3500);
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

        alert()->success("Duo User " . $user->realname . " updated successfully!")->autoclose(3500);;
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

        alert()->success("Duo User Report for " . $user->realname . " submitted successfully!")->autoclose(3500);;
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
        $this->dispatch(new FetchDuoUsers($user->username));

        alert()->success("Duo User Sync for " . $user->realname . " submitted successfully!")->autoclose(3500);;
        return redirect()->back();
    }

    public function migrateUser($id)
    {
        //Create Duo Admin Client
        $duoAdmin = new DuoAdmin();

        //Get the local Duo User account ID
        $insightUser = DuoUser::findorFail($id);

        //Make sure the source user has spaces in their name
        //If not, return an error
        if(!preg_match('/\s/',$insightUser['username'])) {
            \Log::debug('The source Duo User does not have a space in their username', [$insightUser]);
            alert()->error("The source Duo User does not have a space in their username")->persistent('Close');
            return redirect('duo/user/' . $id);
        }

        //Get a fresh copy of the current User data before adding the new user.
        $this->dispatch(new FetchDuoUsers($insightUser->username));

        //Fetch the User details via Duo API
        $res = $duoAdmin->users($insightUser->username);

        //If we didn't get the user object back, error and redirect
        if(!count($res['response']['response']))
        {
            \Log::debug('Source Duo User not found for migrate function', [$insightUser]);
            alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin")->persistent('Close');
            return redirect('duo/user/' . $id);
        }

        //Grab the user details
        $user = $res['response']['response'][0];

        //Implode the explode...  (Remove the space from the username)
        $user['username'] = implode('', explode(' ', $user['username']));

        //Query the Duo API to see if the destination
        //user already exists in Duo
        $res = $duoAdmin->users($user['username']);

        //If we didn't get the user object back, let's create the new Duo user
        if(!count($res['response']['response']))
        {
            //Create the new Duo User
            $res = $duoAdmin->create_user($user['username'],$user['realname'],$user['email'],$user['status'],$user['notes']);

            \Log::debug('Create new Duo User Response', [$res]);

            //If the status is not OK, error and redirect
            if($res['response']['stat'] != "OK")
            {
                \Log::debug('Error while creating new Duo User', [$insightUser,$user,$res]);
                alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin")->persistent('Close');
                return redirect('duo/user/' . $id);
            }

            //Our 'Add Duo User' call was successful.
            //Assign the new user to this variable
            $newDuoUser = $res['response']['response'];

        } else {
            //Our 'Add Duo User' call was successful.
            //Assign the new user to this variable
            $newDuoUser = $res['response']['response'][0];
        }

        //Sync Phones to new Duo User account
        foreach($insightUser->duoPhones()->lists('phone_id')->toArray() as $phone)
        {
            $res = $duoAdmin->user_associate_phone($newDuoUser['user_id'],$phone);
            //If the status is not OK, error and redirect
            if(!$res['response']['stat'] != "OK")
            {
                \Log::debug('Error Associating Phone Res:', [$res]);
                alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin")->persistent('Close');
            }
        }

        //Sync Tokens to new Duo User account
        foreach($insightUser->duoTokens()->lists('token_id')->toArray() as $token)
        {
            $res = $duoAdmin->user_associate_token($newDuoUser['user_id'],$token);
            //If the status is not OK, error and redirect
            if(!$res['response']['stat'] != "OK")
            {
                \Log::debug('Error Associating Token Res:', [$res]);
                alert()->error("Not able to migrate $insightUser->realname.  Please contact the UC-Insight Admin")->persistent('Close');
            }
        }

        //Sync the new Duo User with UC Insight via Duo API
        $this->dispatch(new FetchDuoUsers($newDuoUser['username']));

        alert()->success("Duo User Migration for " . $newDuoUser['realname'] . " processed successfully!");
        return redirect('duo');

    }

    public function registeredUsersReport()
    {
        ini_set('memory_limit', '248M');
        
        //Get the users that have a phone or token
        $phoneUsers = \App\Models\Duo\User::has('duoPhones')->get();
        $tokenUsers = \App\Models\Duo\User::has('duoTokens')->get();

        $users = $phoneUsers->merge($tokenUsers);

        return view('duo.registered-report',compact('users'));
    }


    /*
     * Auth Log Routes
     */


    /**
     *  Display the Auth Logs Index Page
     */
    public function logs()
    {
        return view('duo.authlogs.index');
    }


    /**
     *  Get the Auth Logs table data
     */
    public function logData()
    {

        // Define the SQL query
        $logs = Log::join('duo_users', 'duo_logs.duo_user_id', '=', 'duo_users.id', 'left outer' )
            ->leftJoin('duo_group_duo_user', 'duo_users.id', '=', 'duo_group_duo_user.duo_user_id')
            ->leftJoin('duo_groups', 'duo_groups.id', '=', 'duo_group_duo_user.duo_group_id')
            ->select([
                'duo_logs.integration',
                'duo_logs.factor',
                'duo_logs.device',
                'duo_logs.ip',
                'duo_logs.new_enrollment',
                'duo_logs.reason',
                'duo_logs.result',
                'duo_logs.timestamp',
                'duo_users.username',
                'duo_groups.name'
            ])
            ->orderBy('duo_logs.integration', 'asc');

        // Return the Datatables object from the query
        return Datatables::of($logs)
            ->editColumn('duo_logs.timestamp', function ($log) {
                return $log->timestamp->format('Y/m/d');
            })
            ->make(true);
    }

    /**
     *
     * Export Duo Auth Log data as csv
     *
     */
    public function exportLogData()
    {

        // Define the SQL query
        $logs = Log::join('duo_users', 'duo_logs.duo_user_id', '=', 'duo_users.id', 'left outer' )
            ->leftJoin('duo_group_duo_user', 'duo_users.id', '=', 'duo_group_duo_user.duo_user_id')
            ->leftJoin('duo_groups', 'duo_groups.id', '=', 'duo_group_duo_user.duo_group_id')
            ->select([
                'duo_logs.integration',
                'duo_logs.factor',
                'duo_logs.device',
                'duo_logs.ip',
                'duo_logs.new_enrollment',
                'duo_logs.reason',
                'duo_logs.result',
                'duo_logs.timestamp',
                'duo_users.username',
                'duo_groups.name'
            ])
            ->orderBy('duo_logs.integration', 'asc');

        // Create the Datatables object from the query
        $data = Datatables::of($logs)
            ->editColumn('duo_logs.timestamp', function ($log) {
                return $log->timestamp->format('Y/m/d');
            })
            ->make()->getData(true);

        // Get the query data and column names
        $rowData = $data['data'];
        $columns = $data['input']['columns'];

        // Create the csv column headers
        $headers = [];
        foreach($columns as $column) {
            array_push($headers,  ucfirst($column['data']));
        }
        
        // Create the CsvFile object to store our data
        $csvFile = new CsvFile(storage_path() . '/reports/duo/auth-logs/' . Carbon::now()->toDateTimeString() . '-' . \Auth::user()->username . '.csv');

        // Write the header row
        $csvFile->writeRow(array_slice($headers, 0, 10));
        
        // Write the data rows
        foreach($rowData as $row) {
            $csvFile->writeRow(array_slice($row, 0, 10));
        }

        // Return the csv file
        return response()->download($csvFile);
    }
}
