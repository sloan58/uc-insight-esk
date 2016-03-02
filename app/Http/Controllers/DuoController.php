<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DuoController extends Controller
{
    public function getLogs()
    {
        $duoAdmin = new \DuoAPI\Admin(
            'DI862RPCBG75K3SPHNMM',
            '83tknn36V4XjDuCw11kbkji5tFakyOfpEhizuNen',
            'api-d206e387.duosecurity.com'
        );

        $response = $duoAdmin->authLogs();

        $logs = $response['response']['response'];

        foreach($logs as $log)
        {
            if($log['result'] == 'FAILURE')
            {
                print $log['username'] . " failed login from device " . $log['device'] . " at " . date('c',$log['timestamp']) . " for reason " . $log['reason'] . "\n";
            }
        }

    }
}
