<?php namespace App\Libraries;

use App\User;
use SoapFault;
use SoapClient;
use App\Models\Cluster;
use App\Exceptions\SoapException;

/**
 * Class AxlSoap
 * @package App\Services
 */
class AxlSoap extends SoapClient {

    /**
     * @var resource
     */
    private $cluster;
    /**
     * @var \App\User
     */
    private $user;

    /**
     *
     */
    public function __construct(Cluster $cluster)
    {

        $this->cluster = $cluster;

        parent::__construct(storage_path() . '/app/axl/' . $this->cluster->version . '/AXLAPI.wsdl',
            [
                'trace' => true,
                'exceptions' => true,
                'location' => 'https://' . $this->cluster->ip . ':8443/axl/',
                'login' => $this->cluster->username,
                'password' => $this->cluster->password,
                'stream_context' => $this->cluster->verify_peer ?: stream_context_create(array('ssl' => array('verify_peer' => false, 'verify_peer_name' => false)))
            ]
        );
    }

    /**
     * @param $fault
     * @throws \App\Exceptions\SoapException
     */
    private function processAxlFault($fault)
    {
        \Log::error('Axl Request', [
            $this->__getLastRequestHeaders(),
            $this->__getLastRequest()

        ]);
        \Log::error('Axl Response', [
            $this->__getLastResponseHeaders(),
            $this->__getLastResponse()
        ]);
        throw new SoapException($fault->getMessage());
    }

    /**
     * @param $sql
     * @throws \App\Exceptions\SoapException
     * @return \Exception|SoapFault
     */
    public function executeQuery($sql)
    {
        try {
            return $this->executeSQLQuery([
                'sql' => $sql,
            ]);
        } catch(SoapFault $e) {
            $this->processAxlFault($e);
        }
    }

    /**
     * @throws \App\Exceptions\SoapException
     * @internal param $appUserId
     * @return \Exception|\SoapFault
     */
    public function getAxlUser()
    {
        $userType = 'get' . $this->cluster->user_type;

        try {
            return $this->$userType([
                'userid' => $this->cluster->username
            ]);
        } catch(SoapFault $e) {
            $this->processAxlFault($e);
        }
    }

    /*
     * @param $devices
     * @throws \App\Exceptions\SoapException
     * @internal param $appUserId
     * @return \Exception|SoapFault
     */
    /**
     * @param $devices
     * @return mixed
     */
    public function updateAxlUser($devices)
    {
        $userType = 'update' . $this->cluster->user_type;

        try {
            return $this->$userType([
                'userid' => $this->cluster->username,
                'associatedDevices' => [
                    'device' => $devices
                ]
            ]);
        } catch(SoapFault $e) {
            $this->processAxlFault($e);
        }
    }

}