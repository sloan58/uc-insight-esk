<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Cluster;
use App\Http\Requests;
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

        $activeClusterId = \Auth::user()->activeClusterId();

        $page_title = 'Clusters';
        $page_description = 'List';

        return view('cluster.index', compact('clusters','activeClusterId','page_title', 'page_description'));
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
        $cluster = $this->cluster->firstOrCreate(
            $request->except(['_token','active'])
        );

        $cluster->verify_peer = $request->verify_peer ? true : false;

        $users = User::all();

        foreach($users as $user)
        {
//            TODO: Create front end system to manage users and clsuters
//            TODO: In the meantime, give all users access to every cluster.

//            if($user->hasRole('admins','cluster-managers'))
//            {
//                $user->clusters()->attach($cluster);
//            }

            $user->clusters()->attach($cluster);

        }

        if($request->active)
        {
            \Auth::user()->activateCluster($cluster->id);
        }

        $cluster->save();

        alert()->success($cluster->name . " cluster added successfully");

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
        $activeClusterId = \Auth::user()->activeClusterId();

        $directories = Storage::directories('axl/');
        $versions= [];
        foreach($directories as $directory)
        {
            preg_match('/\/(.*)/',$directory,$matches);
            $versions[$matches[1]] = $matches[1];
        }
        return view('cluster.edit', compact('cluster','activeClusterId','versions'));
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
            \Auth::user()->activateCluster($cluster->id);
        } elseif (!isset($request->active) && \Auth::user()->activeClusterId() == $cluster->id)
        {
            \Auth::user()->deactivateCluster();
        }
        $cluster->fill($request->except(['password','verify_peer']));
        $cluster->save();

        alert()->success($cluster->name . " cluster updated successfully");

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

        alert()->success("Cluster removed successfully");

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
