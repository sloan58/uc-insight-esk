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
        //
    }
}
