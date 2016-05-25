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

        // Set the log $count value to 1000
        $count = 1000;
        $backoff = 10;
        while($count == 1000) {
            \Log::debug('Start Log gathering', ['count' => $count, 'backoff' => $backoff]);

            //Query Duo REST API
            $response = $duoAdmin->logs($this->getMinTime());

            if(isset($response['response']['code']) && $response['response']['code'] == '42901') {
                \Log::debug('Received backoff notice', ['response' => $response, 'backoff' => $backoff]);
                sleep($backoff);
                $backoff += 10;
                continue;
            }

            //Duo SDK puts results in nested array [response][response]
            $logs = $response['response']['response'];
            \Log::debug('Received Duo Response Object.  Adding new entries ', [ 'object-count' => count($logs)]);


            // Loop each log to save
            foreach($logs as $log) {

                // Get the DuoUser ID to create a relation
                $duoUserId = User::where('username', $log['username'])->select('id')->first();

                // Sometimes the 'username' from Duo doesn't exist locally....
                if($duoUserId) {
                    $duoUserId = $duoUserId->toArray();
                    $log['duo_user_id'] = $duoUserId['id'];
                } else {
                    $log['duo_user_id'] = NULL;
                }

                // Save the log
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

            // Set the count to number of logs returned in the last call.
            // If it's less than 1000, we've reached the end of the logs
            \Log::debug('Added new log entries.  Setting count: ', [ 'count' => count($logs)]);
            $count = count($logs);
        }

    }

    /**
     * @return null
     */
    private function getMinTime()
    {
        // Get the last Duo log based on Unix Timestamp
        $lastLog = Log::orderBy('timestamp', 'desc')->first();

        // If there are no logs, there is no $mintime to set
        // Otherwise set it to the latest timestamp in our database
        if (is_null($lastLog)) {
            $mintime = NULL;
        } else {
            $mintime = $lastLog['timestamp'] + 1;
        }

        \Log::debug('Calculated Mintime: ', [ 'mintime' => $mintime, 'lastLog' => $lastLog]);

        return $mintime;
    }
}
