<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Libraries\AxlSoap;
use App\Models\Cluster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HuntPilotForwardController extends Controller
{
    public function getForward($cluster,$huntpilotUuid)
    {
        \Debugbar::disable();

        $dirnUuid = '4391a53e-52d2-abee-46b6-aefc1fa61208';

        $swapArray = [
            'All-DN_pt' => 'Hunt-Pilot-Staging-Unreachable_pt',
            'Hunt-Pilot-Unreachable_pt' => 'All-DN_pt'
        ];

        $cluster = Cluster::where('ip',$cluster)->first();

        $axl = new AxlSoap($cluster);

        $huntPilotObj = $axl->getHuntPilotInfo($huntpilotUuid);
        $dnObj = $axl->getDirn($dirnUuid);

        if($huntPilotObj->return->huntPilot->routePartitionName->_ == 'Hunt-Pilot-Unreachable_pt')
        {
            $text = 'The hunt pilot has been unforwarded';

            //Move Dirn to stagin PT
            $axl->updateDirn($dirnUuid,$swapArray[$dnObj->return->line->routePartitionName->_]);

            //Move Hunt Pilot to All-DN_pt
            $axl->updateHuntPilotInfo($huntpilotUuid,$swapArray[$huntPilotObj->return->huntPilot->routePartitionName->_]);

            //Move Dirn to Unreachable PT
            $axl->updateDirn($dirnUuid,'Hunt-Pilot-Unreachable_pt');

        } elseif($huntPilotObj->return->huntPilot->routePartitionName->_ == 'All-DN_pt') {

            $text = 'The hunt pilot has been forwarded';

            $axl->updateHuntPilotInfo($huntpilotUuid,$swapArray[$huntPilotObj->return->huntPilot->routePartitionName->_]);

            $axl->updateDirn($dirnUuid,$swapArray[$dnObj->return->line->routePartitionName->_]);

            $axl->updateHuntPilotInfo($huntpilotUuid,'Hunt-Pilot-Unreachable_pt');

        } else {

            //Error out gracefully
            $text = 'The hunt pilot forwder has experienced an error.';

        }

        $service = new \Sabre\Xml\Service();

        return response(
            $service->write('CiscoIPPhoneText', [
                'Title' => 'UC Insight Pilot Forwarder',
                'Prompt' => '',
                'Text' => $text
            ])
        )->header('Content-Type', 'text/xml');

    }
}
