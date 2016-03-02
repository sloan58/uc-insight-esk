<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HuntPilotForwardController extends Controller
{
    public function getForward(Request $request)
    {
        \Log::debug('Request:', [$request]);

//        return response('');
    }
}
