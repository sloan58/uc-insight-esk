<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Cluster;
use Illuminate\Console\Command;
use App\Jobs\CheckForCallForwardLoop;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CallManagerCfwdLoop extends Command
{

    use DispatchesJobs;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cucm:cfwd-loop';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check to see if someone forwarded their NIPT line to itself.';

    /**
     * @var \App\Models\Cluster
     */
    private $cluster;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Cluster $cluster)
    {
        parent::__construct();
        $this->cluster = $cluster;


    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        $outFile = 'reports/cfwdLoop/CallForwardLoop-' . Carbon::now()->timestamp .'.csv';
        Storage::put($outFile,'Directory Number,Description,Call Forward Number');

        $clusters = $this->cluster->all();

        foreach($clusters as $cluster)
        {
            $this->dispatch(new CheckForCallForwardLoop($cluster,$outFile));
        }

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.secondaryDialToneReport', [], function($message) use($outFile)
        {
            $message
                ->to(['martin_sloan@ao.uscourts.gov', 'kwang_chong@ao.uscourts.gov','aaron_dhiman@ao.uscourts.gov'])
                ->subject('CUCM None Partition Report')
                ->attach(storage_path("app/$outFile"));
        });
    }
}
