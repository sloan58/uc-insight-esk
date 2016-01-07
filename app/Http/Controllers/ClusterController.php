<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Cluster;
use App\Http\Requests;
use Laracasts\Flash\Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateClusterRequest;
use App\Http\Requests\CreateClusterRequest;

/**
 * Class ClusterController
 * @package App\Http\Controllers
 */
class ClusterController extends Controller
{
    /**
     * @var \App\Cluster
     */
    private $cluster;


    /**
     * @param Cluster $cluster
     */
    public function __construct(Cluster $cluster)
    {
        $this->middleware('auth');
        $this->cluster = $cluster;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if(\Auth::user()->hasRole(['admins']))
        {
            $clusters = $this->cluster->paginate(10);
        } else {
            $clusters = \Auth::user()->clusters()->paginate(10);
        }

        $page_title = 'Clusters';
        $page_description = 'List';

//        if($clusters->isEmpty())
//        {
//            return view('cluster.index',compact('page_title','page_description'));
//        }

        return view('cluster.index', compact('clusters','page_title', 'page_description'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $directories = Storage::directories('axl/');
        $versions= [];
        foreach($directories as $directory)
        {
            preg_match('/\/(.*)/',$directory,$matches);
            $versions[$matches[1]] = $matches[1];
        }

        return view('cluster.create', compact('versions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateClusterRequest|Request $request
     * @return Response
     */
    public function store(CreateClusterRequest $request)
    {
        $cluster = $this->cluster->firstOrNew(
            $request->except(['_token','active'])
        );

        if($request->active)
        {
            \Auth::user()->clusters_id = $cluster->id;
            \Auth::user()->save();
        }
        if($request->verify_peer)
        {
            $cluster->verify_peer = true;
        }
        $cluster->save();

        Flash::success('Cluster added!');

        return redirect()->action('ClusterController@index');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return Response
     * @internal param $cluster
     */
    public function edit($id)
    {
        $cluster = $this->cluster->find($id);

        $directories = Storage::directories('axl/');
        $versions= [];
        foreach($directories as $directory)
        {
            preg_match('/\/(.*)/',$directory,$matches);
            $versions[$matches[1]] = $matches[1];
        }
        return view('cluster.edit', compact('cluster','versions'));
    }


    /**
     * @param UpdateClusterRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateClusterRequest $request, $id)
    {
        $cluster = $this->cluster->find($id);
        $cluster->password = checkPassword($cluster->password,$request->password);
        $cluster->verify_peer = $request->verify_peer ? true : false;

        if($request->active)
        {
            \Auth::user()->clusters_id = $cluster->id;

        } elseif (!isset($request->active) && \Auth::user()->clusters_id == $cluster->id)
        {
            \Auth::user()->clusters_id = null;
        }
        \Auth::user()->save();

        $cluster->save();

        Flash::success('Cluster info updated!');

        return redirect()->action('ClusterController@index');

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @internal param Cluster $cluster
     */
    public function destroy($id)
    {
        $this->cluster->destroy($id);

        Flash::success('Cluster Deleted!');

        return redirect()->action('ClusterController@index');
    }

    /**
     * Delete Confirm
     *
     * @param   int   $id
     * @return  View
     */
    public function getModalDelete($id)
    {
        $error = null;

        $modal_title = trans('cluster/dialog.delete-confirm.title');
        $modal_cancel = trans('general.button.cancel');
        $modal_ok = trans('general.button.ok');

        $cluster = $this->cluster->find($id);
        $modal_route = route('cluster.delete', ['id' => $cluster->id]);

        $modal_body = trans('cluster/dialog.delete-confirm.body', ['id' => $cluster->id, 'name' => $cluster->name]);

        return view('modal_confirmation', compact('error', 'modal_route',
            'modal_title', 'modal_body', 'modal_cancel', 'modal_ok'));

    }
}
