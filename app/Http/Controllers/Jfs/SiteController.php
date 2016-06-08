<?php

namespace App\Http\Controllers\Jfs;

use App\Http\Requests;
use Colors\RandomColor;
use App\Models\Jfs\Site;
use App\Models\Jfs\Task;
use App\Models\Jfs\Workflow;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
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
        $reportData = [];
        $workFlows = Workflow::all();
        $totalSites = Site::all()->count();
        $colors = [
            'red', 'orange', 'yellow', 'green', 'blue', 'purple', 'pink',
        ];

        //No search terms given, get all records
        $sites = Site::paginate(10);

        foreach($workFlows as $flow) {

            $taskNames = $flow->tasks->lists('name')->toArray();
            
            foreach($taskNames as $taskName) {
                
                $res = \DB::select('SELECT DISTINCT count(jfs_tasks.name) AS count FROM jfs_site_jfs_task INNER JOIN jfs_tasks ON jfs_site_jfs_task.jfs_task_id = jfs_tasks.id WHERE jfs_tasks.name = "' . $taskName . '" AND completed = 1 GROUP BY jfs_task_id');
                
                if(count($res)) {
                    $reportData[$flow->name][$taskName]['count'] = number_format($res[0]->count / $totalSites, 3) * 100;
                } else {
                    $reportData[$flow->name][$taskName]['count'] = 0;
                }
                
                $reportData[$flow->name][$taskName]['backgroundColor'] = RandomColor::one([ 'hue' => $colors[array_rand($colors, 1)]]);
                $reportData[$flow->name][$taskName]['hoverBackgroundColor'] = "#00C0EF";
            }
        }

        return view('jfs.sites.index', compact('dt', 'reportData'));

    }

    public function indexDatatables()
    {
        //No search terms given, get all records
        $sites = Site::select('id','name');

        // Return the Datatables object from the query
        return Datatables::of($sites)
            ->addColumn('Completed Tasks', function ($site) {
                return $site->completedTasks(true);
            })
            ->addColumn('Incomplete Tasks', function ($site) {
                return $site->incompleteTasks(true);
            })
            ->editColumn('name', function($site) {
                return '<a class="sql-link" href="sites/' . $site->id . '">' . $site->name . '</a>';
            })
            ->make(true);
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
