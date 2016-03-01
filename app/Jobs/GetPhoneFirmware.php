<?php

namespace App\Jobs;

use App\Jobs\Job;
use Sabre\Xml\Reader;
use GuzzleHttp\Client;
use App\Models\Cluster;
use Keboola\Csv\CsvFile;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class GetPhoneFirmware extends Job implements SelfHandling
{

    use InteractsWithQueue, SerializesModels;
    /**
     * @var array
     */
    private $deviceList;

    /**
     * @var CsvFile
     */
    private $csvFileName;

    /**
     * @var
     */
    private $csv_headers;

    /**
     * Create a new job instance.
     * @param array $deviceList
     * @param $csvFileName
     * @param $csv_headers
     */
    public function __construct(Array $deviceList,$csvFileName,$csv_headers)
    {
        $this->deviceList = $deviceList;
        $this->csvFileName = $csvFileName;
        $this->csv_headers = $csv_headers;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Avoid PHP timeouts when querying large clusters
        set_time_limit(0);

        //Create Sabre XML Reader Object
        $reader = new Reader();

        //Create Guzzle REST client Object
        $client = new Client([
            'connect_timeout' => 2,
            'headers' => [
                'Accept' => 'application/xml',
                'Content-Type' => 'application/xml'
            ]
        ]);

        //Pick up the csv report created in the calling controller
        $csvFile = new CsvFile($this->csvFileName);

        //Write the csv headers
        $csvFile->writeRow(
            explode(',', $this->csv_headers)
        );

        //Loop the devices in $this->deviceList to get firmware info
        foreach($this->deviceList as $device)
        {
            \Log::debug('Still running with', [$device]);

            //Only get firmware if the device is registered
            if($device['IsRegistered'])
            {
                try {
                    //Query the phones web interface for the XML device info
                    $response = $client->get('http://' . $device['IpAddress'] . '/DeviceInformationX');
                } catch (RequestException $e) {
                    \Log::debug('Phone request exception', [$e]);
                    continue;
                }

                //Consume the XML with Sabre XML Reader
                $reader->xml($response->getBody()->getContents());
                //Parse the XML
                $deviceInfoX = $response = $reader->parse();

                //Find the index for XML key holding the Firmware information
                $index = searchMultiDimArray($deviceInfoX['value'],'name','{}versionID');
                //Place the firmware info into our $device array
                $device['Firmware'] = $deviceInfoX['value'][$index]['value'];

            }

            //Write the firmware info to csv
            $csvFile->writeRow($device);

        }
    }
}
