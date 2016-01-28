<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    /**
     * @var Device
     */
    private $device;

    /**
     * @param Device $device
     */
    function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function phoneIndex($phone)
    {
        $phone = $this->device->find($phone);

        $page_title = $phone->name;
        $page_description = 'Details';

        return view('phone.index', compact('phone','page_title','page_description'));
    }
}
