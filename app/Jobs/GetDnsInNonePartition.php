<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Cluster;
use App\Libraries\Utils;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue ;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class GetDnsInNonePartition
 * @package App\Jobs
 */
class GetDnsInNonePartition extends Job implements SelfHandling
{
    /**
     * @var \App\Models\Cluster
     */
    private $cluster;
    /**
     * @var
     */
    private $outFile;

    /**
     * @param Cluster $cluster
     * @param $outFile
     */
    public function __construct(Cluster $cluster,$outFile)
    {
        $this->cluster = $cluster;
        $this->outFile = $outFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Query the CUCM to get numbers in the None partition
        $res = Utils::executeQuery('SELECT dnorpattern, description FROM numplan WHERE fkroutepartition IS NULL AND tkpatternusage = 2',$this->cluster);

        // Create a line in the CSV for the Cluster heading
        Storage::append($this->outFile, $this->cluster->name . ',' . $this->cluster->ip);

        //If we got results.....
        if($res)
        {
            //Add the results to our CSV file
            foreach($res as $dn)
            {
                Storage::append($this->outFile,implode(",",[$dn->dnorpattern,$dn->description]));
            }
        }
    }
}
