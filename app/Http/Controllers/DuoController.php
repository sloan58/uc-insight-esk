<?php

namespace App\Http\Controllers;

use DB;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\Duo\User as DuoUser;

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

    public function showUser($id)
    {
        $user = DuoUser::find($id);

        $reports = \App\Models\Report::where('name','like','%duo%')->get();

        return view('duo.show',compact('user','reports'));
    }

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
}
