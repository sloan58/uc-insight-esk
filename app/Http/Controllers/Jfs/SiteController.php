<?php

namespace App\Http\Controllers\Jfs;

use App\Http\Requests;
use App\Models\Jfs\Site;
use App\Models\Jfs\Task;
use Colors\RandomColor;
use App\Models\Jfs\Workflow;
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
                ->paginate(10);

        } else {

            //No search terms given, get all records
            $sites = Site::paginate(10);

        }

        $reportData = [];
        $workFlows = Workflow::all();
        $totalSites = Site::all()->count();
        $colors = [
            'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'pink', 'monochrome'
        ];

        foreach($workFlows as $flow) {

            $taskNames = $flow->tasks->lists('name')->toArray();
            
            foreach($taskNames as $taskName) {
                
                $res = \DB::select('SELECT DISTINCT count(jfs_tasks.name) AS count FROM jfs_site_jfs_task INNER JOIN jfs_tasks ON jfs_site_jfs_task.jfs_task_id = jfs_tasks.id WHERE jfs_tasks.name = "' . $taskName . '" AND completed = 1 GROUP BY jfs_task_id');
                
                if(count($res)) {
                    $reportData[$flow->name][$taskName]['count'] = number_format($res[0]->count / $totalSites, 3);
                } else {
                    $reportData[$flow->name][$taskName]['count'] = 0;
                }
                
                $reportData[$flow->name][$taskName]['backgroundColor'] = RandomColor::one([ 'hue' => $colors[array_rand($colors, 1)]]);
                $reportData[$flow->name][$taskName]['hoverBackgroundColor'] = "#FF6384";
            }
        }

        return view('jfs.sites.index', compact('sites', 'reportData'));

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
