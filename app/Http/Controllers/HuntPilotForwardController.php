<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Libraries\AxlSoap;
use App\Models\Cluster;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HuntPilotForwardController extends Controller
{
    public function getForward(Request $request)
    {

        \Debugbar::disable();

        /*
         * Collect Service Parameters
         *
         * hunt-pilot-number: The hunt pilot number to flipped
         * pub-ip-address: The CUCM cluster to work with
         */
        $huntPilotNumber = $request->input('hunt-pilot-number');
        $cluster = $request->input('pub-ip-address');

        //Get Cluster details from the local DB
        $cluster = Cluster::where('ip',$cluster)->first();

        //Create a new AxlSoap object
        $axl = new AxlSoap($cluster);

        //Query the CUCM DB to get the pkid for the hunt pilot
        $result = $axl->executeQuery('SELECT pkid FROM numplan WHERE dnorpattern = "' . $huntPilotNumber . '" AND tkpatternusage = 7');
        $this->logger(['AXL SQL fetch Hunt Pilot pkid - ',$axl->__getLastRequest(),$axl->__getLastResponse()]);

        //Make sure the query returned a pkid
        if(isset($result->return->row))
        {
            //Put the hunt pilot pkid into a var
            $huntPilotPkid = $result->return->row->pkid;

            //Get full details of the hunt pilot from CUCM AXL
            $huntPilotObj = $axl->getHuntPilotInfo($huntPilotPkid);
            $this->logger(['getHuntPilotInfo - ',$axl->__getLastRequest(),$axl->__getLastResponse()]);

            //Get the 'sister' directory number pkid
            $result = $axl->executeQuery('SELECT pkid FROM numplan WHERE dnorpattern = "' . $huntPilotNumber . '" AND tkpatternusage = 2');
            $this->logger(['AXL SQL fetch DN pkid - ',$axl->__getLastRequest(),$axl->__getLastResponse()]);

            //Make sure the query returned a pkid
            if(isset($result->return->row))
            {
                //Put the dn pkid into a var
                $dnPkid = $result->return->row->pkid;

                //Check the current route partition setting
                //If it's 'Hunt-Pilot-Unreachable_pt' then we need to unforward
                if($huntPilotObj->return->huntPilot->routePartitionName->_ == 'Hunt-Pilot-Unreachable_pt')
                {
                    //Set the response text
                    $text = 'The hunt pilot has been unforwarded';

                    //Move Dirn to staging PT
                    $axl->updateDirn($dnPkid,'Hunt-Pilot-Staging-Unreachable_pt');
                    $this->logger(['updateDirn - ', $axl->__getLastRequest(),$axl->__getLastResponse()]);

                    //Move Hunt Pilot to All-DN_pt
                    $axl->updateHuntPilotInfo($huntPilotPkid,'All-DN_pt');
                    $this->logger(['updateHuntPilot',$axl->__getLastRequest(),$axl->__getLastResponse()]);

                    //Move Dirn to Unreachable PT
                    $axl->updateDirn($dnPkid,'Hunt-Pilot-Unreachable_pt');
                    $this->logger(['updateDirn - ', $axl->__getLastRequest(),$axl->__getLastResponse()]);


                    //If the hunt pilot partition is 'All-DN_pt' then we need to forward the number
                } elseif($huntPilotObj->return->huntPilot->routePartitionName->_ == 'All-DN_pt') {

                    //Set the response text
                    $text = 'The hunt pilot has been forwarded';

                    //Move the hunt pilot to the staging pt
                    $axl->updateHuntPilotInfo($huntPilotPkid,'Hunt-Pilot-Staging-Unreachable_pt');
                    $this->logger(['updateHuntPilot',$axl->__getLastRequest(),$axl->__getLastResponse()]);

                    //Move the DN to the All-DN_pt
                    $axl->updateDirn($dnPkid,'All-DN_pt');
                    $this->logger(['updateDirn - ', $axl->__getLastRequest(),$axl->__getLastResponse()]);

                    //Move the hunt pilot to the unreachable PT
                    $axl->updateHuntPilotInfo($huntPilotPkid,'Hunt-Pilot-Unreachable_pt');
                    $this->logger(['updateHuntPilot',$axl->__getLastRequest(),$axl->__getLastResponse()]);

                } else {

                    //Something went wrong....
                    $text = 'The hunt pilot forwarder has experienced an error.';
                    $this->logger([$huntPilotObj]);


                }

            } else {

                $text = 'The hunt pilot forwarding DN could not be found.  Please contact the system administrator.';

            }

        } else {

            $text = 'The hunt pilot identifier could not be found.  Please contact the system administrator.';

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

    private function logger(Array $message)
    {
        \Log::debug('Hunt Pilot Debug:',$message);
    }
}
