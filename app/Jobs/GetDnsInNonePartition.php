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
        $res = Utils::executeQuery('SELECT dnorpattern, description FROM numplan WHERE fkroutepartition IS NULL AND tkpatternusage = 2',$this->cluster);

        Storage::append($this->outFile, $this->cluster->name . ',' . $this->cluster->ip);

        if($res)
        {
            foreach($res as $dn)
            {
                Storage::append($this->outFile,implode(",",[$dn->dnorpattern,$dn->description]));
            }
        }
    }
}
