<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Duo\Group;
use App\Libraries\DuoAdmin;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class FetchDuoGroups extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        set_time_limit(0);
        \Log::debug('Set PHP time limit to zero (no limit)');

        //Create the Duo Admin Client and set the timeout higher than default
        $duoAdmin = new DuoAdmin();
        $duoAdmin->setRequesterOption('timeout','6000000');
        \Log::debug('Created new DuoAdmin object', [$duoAdmin]);

        $response = $duoAdmin->groups();
        $groups = $response['response']['response'];
        \Log::debug('Obtained Groups from Duo API - ', [count($groups)]);

        //Loop Duo Groups
        foreach($groups as $group)
        {
//            \Log::debug('Processing Duo Group', [$group]);
            
            //Get an existing Duo Group or create a new one
            $duoGroup = Group::firstOrCreate([
                'group_id' => $group['group_id']
            ]);

            //Update Duo Group Settings
            $duoGroup->name = $group['name'];
            $duoGroup->desc = $group['desc'];
            $duoGroup->status = $group['status'];
            $duoGroup->mobile_otp_enabled = $group['mobile_otp_enabled'];
            $duoGroup->push_enabled = $group['push_enabled'];
            $duoGroup->sms_enabled = $group['sms_enabled'];
            $duoGroup->voice_enabled = $group['voice_enabled'];

            //Save Duo Group
            $duoGroup->touch();
            $duoGroup->save();

        }
        \Log::debug('Completed FetchDuoGroups Job');
    }
}
