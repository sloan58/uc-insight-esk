<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Illuminate\Http\Request;
use App\Libraries\ControlCenterSoap;
use App\Http\Controllers\Controller;

class ReportingController extends Controller
{
    public function servicesIndex()
    {
        $cluster = \Auth::user()->activeCluster();
        $data = \App\Libraries\Utils::executeQuery('SELECT name FROM processnode WHERE tkprocessnoderole = 1 AND name != "EnterpriseWideData"',$cluster);

        $clusterStatus = [];
        foreach($data as $node)
        {
            $ris = new ControlCenterSoap($cluster);
            $clusterStatus[$node->name] = $ris->getServiceStatus();
        }

        return view('reports.services.show', compact('clusterStatus'));
    }
}
