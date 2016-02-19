<?php
namespace App\Libraries;

use SoapFault;
use SoapClient;
use App\Models\Cluster;
use App\Exceptions\SoapException;

class ControlCenterSoap extends SoapClient {

    /**
     * @var resource
     */
    private $cluster;
    /**
     * @var
     */
    private $currentNodeIpAddress;


    /**
     * @param Cluster $cluster
     */
    public function __construct(Cluster $cluster, $currentNodeIpAddress)
    {
        $this->cluster = $cluster;
        $this->currentNodeIpAddress = $currentNodeIpAddress;

        parent::__construct('https://' . $this->currentNodeIpAddress . '/controlcenterservice2/services/ControlCenterServices?wsdl',
            [
                'trace' => true,
                'exceptions' => true,
                'location' => 'https://' . $this->currentNodeIpAddress . ':8443/controlcenterservice2/services/ControlCenterServices',
                'login' => $this->cluster->username,
                'password' => $this->cluster->password,
                'stream_context' => $this->cluster->verify_peer ?: stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false)))
            ]
        );
    }

    /**
     * @return mixed
     * @throws SoapException
     */
    public function getServiceStatus()
    {
        try {

            $status =  $this->soapGetServiceStatus([
                'ServiceStatus' => ''
            ]);

        } catch (SoapFault $e) {

            throw new SoapException($e->faultstring);

        }

        return $status->soapGetServiceStatusReturn->ServiceInfoList->item;
    }
}