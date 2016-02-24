<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Report;
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

        //Get all reports where type = cucm_daily
        $reports = Report::where('type','cucm_daily')->get();

        //Get all configured CUCM clusters
        $clusters = $this->cluster->all();

        //Set timestamp for file names
        $timeStamp = Carbon::now('America/New_York')->toDateTimeString();

        //Create array to track attachments
        $attachments = [];

        //Loop reports
        foreach($reports as $index => $report)
        {
            $attachments[$index] = $report->path . $report->name . '-' . $timeStamp .'.csv';

            //Persist report to disk
            Storage::put($attachments[$index],$report->csv_headers);

            //Loop each cluster and run the reports
            foreach($clusters as $cluster)
            {
                $this->dispatch(new $report->job($cluster,$attachments[$index]));
            }
        }

        //Reports are done running, let's email to results
        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.cucmDailyReporting', [], function($message) use($attachments)
        {
            //TODO: Create system for users to manage report subscriptions.
            $message
                ->to(['martin_sloan@ao.uscourts.gov', 'kwang_chong@ao.uscourts.gov','aaron_dhiman@ao.uscourts.gov','minh_leung@ao.uscourts.gov'])
                ->subject('CUCM Daily Report');

                //Add all reports to email
                foreach($attachments as $report)
                {
                    $message->attach(storage_path($report));
                }
        });

    }
}
