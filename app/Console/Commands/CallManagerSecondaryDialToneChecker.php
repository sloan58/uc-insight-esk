<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Cluster;
use Illuminate\Console\Command;
use App\Jobs\GetDnsInNonePartition;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;


/**
 * Class CallManagerSecondaryDialToneChecker
 * @package App\Console\Commands
 */
class CallManagerSecondaryDialToneChecker extends Command
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
     * @var \App\Models\Cluster
     */
    private $cluster;


    /**
     * @param Cluster $cluster
     */
    public function __construct(Cluster $cluster)
    {
        parent::__construct();
        $this->cluster = $cluster;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $outFile = 'reports/nonePt/CucmNonePartition-' . Carbon::now()->timestamp .'.csv';
        Storage::put($outFile,'Directory Number,Description');

        $clusters = $this->cluster->all();

        foreach($clusters as $cluster)
        {
            $this->dispatch(new GetDnsInNonePartition($cluster,$outFile));
        }

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.secondaryDialToneReport', [], function($message) use($outFile)
        {
            $message
//                ->from('UC-Insight-Reporting@laireight.com')
                ->to('martinsloan58@gmail.com', 'Marty Sloan')
                ->subject('CUCM None Partition Report')
                ->attach(storage_path("app/$outFile"));
        });
    }
}
