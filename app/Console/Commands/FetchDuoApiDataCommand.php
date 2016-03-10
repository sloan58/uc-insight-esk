<?php

namespace App\Console\Commands;

use App\Jobs\FetchDuoUsers;
use App\Jobs\FetchDuoGroups;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class FetchDuoApiDataCommand extends Command
{
    use DispatchesJobs;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duo:fetch-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the Duo API User and Group Data';

    /**
     * Create a new command instance.
     *
     * @return \App\Console\Commands\FetchDuoApiDataCommand
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
        $this->dispatch(new FetchDuoGroups());
        $this->dispatch(new FetchDuoUsers());
    }
}
