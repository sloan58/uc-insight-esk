<?php

namespace App\Console\Commands;

use App\Jobs\GetDnsInNonePartition;
use App\Models\Cluster;
use App\Models\Sql;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;


class CheckCucmNonePartition extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cucm:none-pt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for DN\'s in the CUCM None Partition.';

    /**
     * @var \App\Models\Sql
     */
    private $sql;
    /**
     * @var \App\Models\Cluster
     */
    private $cluster;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->dispatch(new GetDnsInNonePartition());
    }
}
