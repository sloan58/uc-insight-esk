<?php

namespace App\Api\Controllers;


use App\Http\Requests;
use App\Models\Cluster;
use App\Libraries\AxlSoap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhoneController extends Controller
{

    public function resetPhone(Request $request)
    {
        //Get request input
        $phone = $request->input('name');
        $cluster = $request->input('cluster');

        //Get the cluster for the phone
        $cluster = Cluster::where('ip',$cluster)->first();

        //Create AXL client
        $axl = new AxlSoap($cluster);

        //Attempt a phone reset
        $response = $axl->phoneReset($phone);

        //Return response
        if(isset($response->return))
        {
            return json_encode($response->return);
        } else {
            return $response;
        }

    }
}
