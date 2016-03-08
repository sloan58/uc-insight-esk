<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Report;
use App\Models\Cluster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateRegisteredDuoUsersCommand extends Command implements SelfHandling
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        //Get all reports where type = cucm_daily
        $reports = Report::where('type','duo_registered_users')->get();

        //Set timestamp for file names
        $timeStamp = Carbon::now('America/New_York')->toDateTimeString();

        //Create array to track attachments
        $attachments = [];
    }
}
