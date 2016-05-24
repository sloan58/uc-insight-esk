<?php

namespace App\Jobs;


use App\Jobs\Job;
use App\Models\Duo\Log;
use App\Models\Duo\User;
use App\Libraries\DuoAdmin;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;


/**
 * Class FetchDuoUsers
 * @package App\Jobs
 */
class FetchDuoAuthLogs extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, DispatchesJobs;


    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        //Create the Duo Admin Client and set the timeout higher than default
        $duoAdmin = new DuoAdmin();
        $duoAdmin->setRequesterOption('timeout','6000000');

        //Query Duo REST API
        $response = $duoAdmin->logs();

        //Duo SDK puts results in nested array [response][response]
        $logs = $response['response']['response'];

        foreach($logs as $log) {


            $duoUserId = User::where('username',$log['username'])->select('id')->first();
            if($duoUserId) {
                $duoUserId = $duoUserId->toArray();
                $log['duo_user_id'] = $duoUserId['id'];
            } else {
                $log['duo_user_id'] = NULL;
            }

            \Log::debug('Log ', $log);

            Log::create($log);
            
//            $record = Log::firstOrCreate([
//                'username' => $log['username'],
//                'timestamp' => $log['timestamp']
//            ]);
//
//            $record->device = $log['device'];
//            $record->factor = $log['factor'];
//            $record->integration = $log['integration'];
//            $record->ip = $log['ip'];
//            $record->new_enrollment = $log['new_enrollment'];
//            $record->reason = $log['reason'];
//            $record->result = $log['result'];
//            $record->duo_user_id = $log['duo_user_id'];
//
//            $record->save();

        }

    }
}
