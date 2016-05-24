<?php

namespace App\Console\Commands;

use App\Jobs\FetchDuoAuthLogs;
use App\Jobs\FetchDuoUsers;
use App\Jobs\FetchDuoGroups;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class FetchDuoAuthLogDataCommand extends Command
{
    use DispatchesJobs;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duo:fetch-authlogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the Duo API Authentication Log Data';


    /**
     * FetchDuoAuthLogDataCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $this->dispatch(new FetchDuoAuthLogs());
    }
}
