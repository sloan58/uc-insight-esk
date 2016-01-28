<?php namespace App\Libraries;

use App\User;
use SoapFault;
use SoapClient;
use App\Cluster;
use App\Exceptions\SoapException;

class RisSoap extends SoapClient{

    /**
     * @var resource
     */
    private $cluster;

    public function __construct(User $user = null)
    {
        if(is_null($user))
        {
            if(!\Auth::user()->activeCluster()) {
                throw new SoapException("You have no Active Cluster Selected");
            }
            $this->cluster = \Auth::user()->activeCluster();
        } else {
            $this->user = $user;
            $this->cluster = $this->user->activeCluster();
        }

        parent::__construct(storage_path() . '/app/sxml/RISAPI.wsdl',
            [
                'trace' => true,
                'exceptions' => true,
                'location' => 'https://' . $this->cluster->ip . ':8443/realtimeservice/services/RisPort',
                'login' => $this->cluster->username,
                'password' => $this->cluster->password,
                'stream_context' => $this->cluster->verify_peer ?: stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false)))
            ]
        );
    }


    /**
     * @param $phoneArray
     * @return bool|\Exception|SoapFault
     * @throws SoapException
     */
    public function getDeviceIp($phoneArray)
    {
        try {
            $response = $this->SelectCmDevice('',[
                'MaxReturnedDevices'=>'1000',
                'Class'=>'Any',
                'Model'=>'255',
                'Status'=>'Any',
                'NodeName'=>'',
                'SelectBy'=>'Name',
                'SelectItems'=>
                    $phoneArray
            ]);
        } catch (SoapFault $e) {

            /*
             * Loop if RISPort error for exceeding maximum calls in 1 minute
             * The typo in the error message is not mine, it is courtesy of Cisco :-(
             */
            if (preg_match('/^AxisFault: Exceeded allowed rate for Reatime information/',$e->faultstring))
            {
                sleep(30);
                $this->getDeviceIp($phoneArray);
            }

            // It's a real error, let's bail!
            throw new SoapException($e->faultstring);
        }
        return $response["SelectCmDeviceResult"];
    }


    /**
     * @param $phones
     * @return array
     */
    public function createRisPhoneArray($phones)
    {
        $deviceArray = [];

        foreach ($phones as $i)
        {
            $deviceArray[]['Item'] = $i;
        }
        return $deviceArray;
    }

    /**
     * @param $risResults
     * @param $phoneArray
     * @return mixed
     */
    public function processRisResults($risResults,$phoneArray)
    {
        $i = 0;

        foreach($phoneArray as $k => $v)
        {
            $deviceAndIp[$i]['DeviceName'] = $v['Item'];

            if(isset($risResults->CmNodes))
            {
                foreach ($risResults->CmNodes as $cmNode)
                {
                    if (!isset($cmNode->CmDevices[0])) continue;

                    list($deviceAndIp[$i]['IpAddress'],$deviceAndIp[$i]['IsRegistered'],$deviceAndIp[$i]['Description'],$deviceAndIp[$i]['Product']) = $this->searchForIp($cmNode->CmDevices,$deviceAndIp[$i]['DeviceName']);

                    if (filter_var($deviceAndIp[$i]['IpAddress'], FILTER_VALIDATE_IP)) break;
                }
            }
            if (!isset($deviceAndIp[$i]['IpAddress']))
            {
                $deviceAndIp[$i]['IpAddress'] = "Unregistered/Unknown";
                $deviceAndIp[$i]['IsRegistered'] = false;
                $deviceAndIp[$i]['Description'] = "Unavailable";
                $deviceAndIp[$i]['Model'] = "Unavailable";
                $deviceAndIp[$i]['Product'] = "Unavailable";
            }
            $i++;
        }
        return $deviceAndIp;
    }

    /**
     * @param $array
     * @param $value
     * @return bool
     */
    private function searchForIp($array,$value)
    {

        foreach($array as $device)
        {
            if($device->Name == $value && $device->Status == "Registered")
            {
                return [$device->IpAddress,true,$device->Description,$device->Product];
            }
        }
        return false;
    }
}