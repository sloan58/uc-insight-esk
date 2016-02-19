<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use App\Libraries\Utils;
use Illuminate\Http\Request;
use App\Libraries\ControlCenterSoap;
use App\Http\Controllers\Controller;

class ReportingController extends Controller
{
    public function servicesIndex()
    {
        //Get the active cluster for the logged in user
        $cluster = \Auth::user()->activeCluster();

        //Set the SQL query based on the CUCM DB version
        switch($cluster->version)
        {
            case (preg_match('/10/', $cluster->version) ? true : false):
                $data = Utils::executeQuery('SELECT name FROM processnode WHERE tkprocessnoderole = 1 AND name != "EnterpriseWideData"',$cluster);
                break;
            case (preg_match('/9/', $cluster->version) ? true : false):
                $data = Utils::executeQuery('SELECT name FROM processnode WHERE name != "EnterpriseWideData"',$cluster);
                break;
        }

        //Loop each node in the cluster to get all services status
        $clusterStatus = [];
        foreach($data as $node)
        {
            $ris = new ControlCenterSoap($cluster,$node->name);
            $clusterStatus[$node->name] = $ris->getServiceStatus();
        }

        return view('reports.services.show', compact('clusterStatus'));
    }

    public function registrationIndex()
    {
        // Avoid PHP timeouts when querying large clusters
        set_time_limit(0);

        $cluster = \Auth::user()->activeCluster();

        // Query CUCM for device name and model
        $data = Utils::executeQuery('SELECT d.name devicename, t.name model FROM device d INNER JOIN typemodel t ON d.tkmodel = t.enum',$cluster);

        // $deviceList will hold our array for RisPort
        $deviceList = [];

        // Loop SQL data and assign devicename to $deviceList
        foreach($data as $key => $val)
        {
            $deviceList[$key]['DeviceName'] = $val->devicename;
        }

        $registrationReport = \App\Libraries\Utils::generateEraserList($deviceList,$cluster);

        return view('reports.registration.show', compact('registrationReport'));

    }
}
