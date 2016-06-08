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
        $backoff = NULL;
        while($count == 1000) {

            \Log::debug('Start Log gathering', ['count' => $count, 'backoff' => $backoff]);

            //Query Duo REST API
            $response = $duoAdmin->logs($this->getMinTime());

            if(isset($response['response']['code']) && $response['response']['code'] == '42901') {

                $backoff += 10;

                \Log::debug('Received backoff notice', ['response' => $response, 'set-backoff' => $backoff]);

                sleep($backoff);
                continue;
            }

            //Duo SDK puts results in nested array [response][response]
            $logs = $response['response']['response'];

            \Log::debug('Received Duo Response Object.  Adding new entries ', [ 'object-count' => count($logs)]);

            // Loop each log to save
            foreach($logs as $log) {

                // Get the DuoUser ID to create a relation
                $duoUserId = User::where('username', $log['username'])->first();

                // Sometimes the 'username' from Duo doesn't exist locally....
                if($duoUserId) {
                    $log['duo_user_id'] = $duoUserId->id;
                } else {
                    $log['duo_user_id'] = NULL;
                }

                // Save the log
                Log::create($log);
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
        if (is_null($lastLog['timestamp'])) {
            $mintime = NULL;
        } else {
            $mintime = $lastLog['timestamp']->timestamp + 1;

        }

        \Log::debug('Calculated Mintime: ', [ 'mintime' => $mintime, 'lastLog' => $lastLog]);

        return $mintime;
    }
}
