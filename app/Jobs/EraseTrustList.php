<?php

namespace App\Jobs;

use App\Models\Eraser;
use App\Libraries\Utils;
use App\Models\IpAddress;
use App\Libraries\PhoneDialer;
use App\Models\Device as Phone;
use Illuminate\Contracts\Bus\SelfHandling;

class EraseTrustList extends Job implements SelfHandling
{

    private $eraserList;
    /**
     * @var Utils
     */
    private $utils;

    /**
     * Create a new job instance.
     *
     * @param array $eraserList
     * @internal param \App\Libraries\Utils $utils
     */
    public function __construct(Array $eraserList)
    {
        $this->eraserList = $eraserList;
        $this->utils = new Utils;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $formattedEraserList = $this->utils->generateEraserList($this->eraserList);

        foreach($formattedEraserList as $device)
        {
            //Create the Phone
            $phone = Phone::firstOrCreate([
                'name' => $device['DeviceName'],
                'description' => $device['Description'],
                'model' => $device['Model']
            ]);

            // Create the IpAddress
            $ipAddress = IpAddress::firstOrCreate([
                'ip_address' => $device['IpAddress']
            ]);

            // Attach the Phone and IpAddress
            $phone->ipAddresses()->sync([$ipAddress->id],false);

            //Start creating Eraser
            $tleObj = Eraser::create([
                'device_id' => $phone->id,
                'ip_address_id' => $ipAddress->id,
                'type' => $device['type']
            ]);

            if(isset($device['bulk_id']))
            {
                $tleObj->bulks()->attach($device['bulk_id']);
            }

            if($device['IpAddress'] == "Unregistered/Unknown")
            {
                $tleObj->result = 'Fail';
                $tleObj->fail_reason = 'Unregistered/Unknown';
                $tleObj->save();
                continue;
            }

            $keys = setKeys($device['Model'],$device['type']);

            if(!$keys)
            {
                $tleObj->result = 'Fail';
                $tleObj->fail_reason = 'Unsupported Model';
                $tleObj->save();
                return;
            }
            $dialer = new PhoneDialer($device['IpAddress']);
            $status = $dialer->dial($tleObj,$keys);

            //Successful if returned true
            $passFail = $status ? 'Success' : 'Fail';
            $tleObj->result = $passFail;
            $tleObj->save();
        }
    }
}
