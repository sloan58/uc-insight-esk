<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Cluster;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;


/**
 * Class CallManagerReportingCommand
 * @package App\Console\Commands
 */
//class CallManagerSecondaryDialToneChecker extends Command
class CallManagerReportingCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cucm:reports';

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
        //Create our report outfile names
        $reports['secondaryDialToneOutFile']['fileName'] = 'reports/nonePt/CucmNonePartition-' . Carbon::now()->timestamp .'.csv';
        $reports['secondaryDialToneOutFile']['Job'] = 'App\Jobs\GetDnsInNonePartition';

        $reports['callForwardLoopOutFile']['fileName'] = 'reports/cfwdLoop/CallForwardLoop-' . Carbon::now()->timestamp .'.csv';
        $reports['callForwardLoopOutFile']['Job'] = 'App\Jobs\CheckForCallForwardLoop';


        //Write the reports to disk
        Storage::put($reports['secondaryDialToneOutFile']['fileName'],'Directory Number,Description');
        Storage::put($reports['callForwardLoopOutFile']['fileName'],'Directory Number,Description');

        foreach($reports as $report)
        {
            //Get all configured CUCM clusters
            $clusters = $this->cluster->all();

            //Loop each cluster and run the reports
            foreach($clusters as $cluster)
            {
                $this->dispatch(new $report['Job']($cluster,$report['fileName']));
            }
        }

        //TODO: Fix this
        $secondaryDialToneReport = $reports['secondaryDialToneOutFile']['fileName'];
        $callForwardLoopOutFile = $reports['callForwardLoopOutFile']['fileName'];

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.cucmDailyReporting', [], function($message) use($secondaryDialToneReport,$callForwardLoopOutFile)
        {
            $message
                ->to(['martin_sloan@ao.uscourts.gov', 'kwang_chong@ao.uscourts.gov','aaron_dhiman@ao.uscourts.gov'])
                ->subject('CUCM Daily Report')
                ->attach(storage_path("app/$secondaryDialToneReport"))
                ->attach(storage_path("app/$callForwardLoopOutFile"));
        });
    }
}
