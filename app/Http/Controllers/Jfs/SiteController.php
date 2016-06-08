<?php

namespace App\Http\Controllers\Jfs;

use App\Http\Requests;
use App\Models\Jfs\Site;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Libraries\Jfs\SiteDashboardService;



/**
 * Class SiteController
 * @package App\Http\Controllers\Jfs
 */
class SiteController extends Controller
{
    /**
     * @var SiteDashboardService
     */
    private $siteDashboardService;

    /**
     * SiteController constructor.
     * @param SiteDashboardService $siteDashboardService
     */
    public function __construct(SiteDashboardService $siteDashboardService)
    {
        $this->siteDashboardService = $siteDashboardService;
    }


    /**
     * Return the Site Task Completion graphs
     *
     * @return \BladeView|bool|\Illuminate\View\View
     */
    public function index()
    {
        $graphData = $this->siteDashboardService->generateGraphData();
        
        return view('jfs.sites.index', compact('graphData'));

    }

    /**
     * Return site task completion counts
     * Used with serverSide Datatables
     * 
     * @return mixed
     */
    public function indexDatatables()
    {
        //Get all Site objects
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

    /**
     * AJAX handler to update task statuses
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if($request->ajax()) {

            $task = $request->input('task');
            $site = Site::find($request->input('site'));
            $site->tasks()->updateExistingPivot($task,[ 'completed'=> !$site->tasks()->where('id',$task)->first()->pivot->completed] );

            return response()->json($site->id);
        }
    }
}
