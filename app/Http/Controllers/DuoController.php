<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DuoController extends Controller
{

    function __construct()
    {
        $this->duo = new \DuoAPI\Admin(
            'DI862RPCBG75K3SPHNMM',
            '83tknn36V4XjDuCw11kbkji5tFakyOfpEhizuNen',
            'api-d206e387.duosecurity.com'
        );
    }

    public function getLogs()
    {

        $response = $this->duo->authLogs();

        $logs = $response['response']['response'];

        foreach($logs as $log)
        {
            if($log['result'] == 'FAILURE')
            {
                print $log['username'] . " failed login from device " . $log['device'] . " at " . date('c',$log['timestamp']) . " for reason " . $log['reason'] . "\n";
            }
        }

    }

    public function getUsers()
    {
        dd($this->duo->users());
    }
}
