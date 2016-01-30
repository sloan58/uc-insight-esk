<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Eraser;
use App\Models\Cluster;
use App\Models\IpAddress;
use App\Libraries\PhoneDialer;
use App\Models\Device as Phone;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ControlPhone extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var \App\Models\Device
     */
    private $device;
    /**
     * @var \App\User
     */
    private $user;
    /**
     * @var Cluster
     */
    private $cluster;


    public function __construct($device,Cluster $cluster)
    {
        $this->device = $device;
        $this->cluster = $cluster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Create the Phone
        $phone = Phone::firstOrCreate([
            'name' => $this->device['DeviceName'],
            'description' => $this->device['Description'],
            'model' => $this->device['Model']
        ]);

        // Create the IpAddress
        $ipAddress = IpAddress::firstOrCreate([
            'ip_address' => $this->device['IpAddress']
        ]);

        // Attach the Phone and IpAddress
        $phone->ipAddresses()->sync([$ipAddress->id],false);

        //Start creating Eraser
        $tleObj = Eraser::create([
            'device_id' => $phone->id,
            'ip_address_id' => $ipAddress->id,
            'type' => $this->device['type']
        ]);


        if(isset($this->device['bulk_id']))
        {
            $tleObj->bulks()->attach($this->device['bulk_id']);
        }

        if($this->device['IpAddress'] == "Unregistered/Unknown")
        {
            $tleObj->result = 'Fail';
            $tleObj->fail_reason = 'Unregistered/Unknown';
            $tleObj->save();
        }

        $keys = setKeys($this->device['Model'],$this->device['type']);

        if(!$keys)
        {
            $tleObj->result = 'Fail';
            $tleObj->fail_reason = 'Unsupported Model';
            $tleObj->save();
            \Log::debug('Bulk', [$tleObj]);

            return;
        }

        $dialer = new PhoneDialer($tleObj,$this->cluster);
        $status = $dialer->dial($keys);

        //Successful if returned true
        $passFail = $status ? 'Success' : 'Fail';
        $tleObj->result = $passFail;
        $tleObj->save();
    }
}
