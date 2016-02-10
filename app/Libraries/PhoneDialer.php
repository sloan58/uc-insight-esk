<?php namespace App\Libraries;


use Sabre\Xml\Reader;
use GuzzleHttp\Client;
use App\Models\Eraser;
use App\Models\Cluster;
use Illuminate\Support\Facades\Log;
use App\Exceptions\PhoneDialerException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

/**
 * Class PhoneDialer
 * @package App\Libraries
 */
class PhoneDialer {



    function __construct(Eraser $tleObj,Cluster $cluster)
    {
        $this->cluster = $cluster;
        $this->tleObj = $tleObj;

        $this->client = new Client([
            'base_uri' => 'http://' . $this->tleObj->ipAddress()->first()->ip_address,
            'verify' => false,
            'connect_timeout' => 2,
            'headers' => [
                'Accept' => 'application/xml',
                'Content-Type' => 'application/xml'
            ],
            'auth' => [
                $this->cluster->username, $this->cluster->password
            ],
        ]);

        $this->reader = new Reader;
        $this->cluster = $cluster;
    }

    /**
     * @param $keys
     * @return bool
     * @throws \App\Exceptions\PhoneDialerException
     */
    public function dial($keys)
    {

        // TODO: Fix timeout issue which returns 503 bad gateway.  Need to fail better.

        $mac = $this->tleObj->device()->first()->name;
        $ip = $this->tleObj->ipAddress()->first()->ip_address;

        foreach ($keys as $index => $key)
        {
            if ($key == "Key:Sleep")
            {
                sleep(2);
                continue;
            }

            $xml = 'XML=<CiscoIPPhoneExecute><ExecuteItem Priority="0" URL="' . $key . '"/></CiscoIPPhoneExecute>';

            try {

                $response = $this->client->post('http://' . $ip . '/CGI/Execute',['body' => $xml]);

                //Workaround for USC NAT
                //$response = $this->client->post('http://10.134.174.64/CGI/Execute',['body' => $xml]);

            } catch (RequestException $e) {

                /*
                 * Handle an exception from the Guzzle client itself
                 */
                if($index == 0)
                {
                    if($e instanceof ClientException)
                    {
                        //Unauthorized
                        $failReason = "Authentication Exception";
                        $exceptionMessage = "$mac @ $ip Authentication Exception";

                    }
                    elseif($e instanceof ConnectException)
                    {
                        //Can't Connect
                        $failReason = "Connection Exception";
                        $exceptionMessage = "$mac @ $ip Connection Exception";
                    }
                    else
                    {
                        //Other exception
                        $failReason = "Unknown Exception";
                        $exceptionMessage = "$mac @ $ip " . $e->getMessage();
                    }

                    $this->tleObj->fail_reason = $failReason;
                    $this->tleObj->save();
                    throw new PhoneDialerException($exceptionMessage);

                    break;

                } else {
                    \Log::error('Guzzle error after successful messages have been sent.  We are on message #' . ($index + 1),[$e]);

                    break;
                }

            }

            /*
             * Get the content from the Guzzle response
             */
            $this->reader->xml($response->getBody()->getContents());
            $response = $this->reader->parse();

            if(isset($response['CiscoIPPhoneResponse']))
            {
                Log::info('dial(),response', [$response]);

            }
            /*
             * Handle an error from the IP phone
             * This indicates the Guzzle messaging was successful
             * but the phone returned an API error
             */
            elseif(isset($response['name']) &&  $response['name'] == '{}CiscoIPPhoneError')
            {
                //Log an Error
                switch($response['attributes']['Number'])
                {
                    case 4:
                        $errorType = 'Authentication Exception';
                        break;
                    case 6:
                        $errorType = 'Invalid URL Exception';
                        break;
                    default:
                        $errorType = 'Unknown Exception';
                        break;
                }

                /*
                 * Persist the error and break
                 */
                $this->tleObj->fail_reason = $errorType;
                $this->tleObj->result = "Fail";
                $this->tleObj->save();
                throw new PhoneDialerException("$mac @ $ip $errorType");

                break;
            }

        }
        return true;
    }

}