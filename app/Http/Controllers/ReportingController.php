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
        $cluster = \Auth::user()->activeCluster();
        $data = Utils::executeQuery('SELECT name FROM processnode WHERE tkprocessnoderole = 1 AND name != "EnterpriseWideData"',$cluster);

        $clusterStatus = [];
        foreach($data as $node)
        {
            $ris = new ControlCenterSoap($cluster);
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
