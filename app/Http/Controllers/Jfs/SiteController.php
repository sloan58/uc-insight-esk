<?php

namespace App\Http\Controllers\Jfs;

use App\Http\Requests;
use App\Models\Jfs\Site;
use App\Models\Jfs\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


/**
 * Class SiteController
 * @package App\Http\Controllers\Jfs
 */
class SiteController extends Controller
{
    /**
     * Return a list of all Sites
     *
     * @param Request $request
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //Check if there was a search parameter sent
        if($request->input('search'))
        {
            //Get the search term
            $search = $request->get('search');

            //Filter our query
            $sites = Site::where('name', 'like', "%$search%")
                ->paginate(15);

        } else {

            //No search terms given, get all records
            $sites = Site::paginate(15);

        }

        return view('jfs.sites.index', compact('sites'));

    }

    /**
     * Get all detail for a Site
     * @param $id
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function show($id)
    {
        $site = Site::find($id);

        return view('jfs.sites.show', compact('site'));
    }

    public function update(Request $request)
    {
        if($request->ajax()) {

            $task = $request->input('task');
            $site = Site::find($request->input('site'));
            $site->tasks()->updateExistingPivot($task,[ 'completed'=> !$site->tasks()->where('id',$task)->first()->pivot->completed] );

//            return response()->json('{ success: true }');
            return response()->json($site->id);
        }
    }
}
