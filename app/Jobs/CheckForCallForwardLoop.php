<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Cluster;
use App\Libraries\Utils;
use Illuminate\Contracts\Bus\SelfHandling;
use Storage;

class CheckForCallForwardLoop extends Job implements SelfHandling
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
        //Query the CUCM to get all DN's, descriptions and what number (if any) they are forwarded to
        $res = Utils::executeQuery('SELECT n.dnorpattern, n.description, cfwd.cfadestination FROM numplan n INNER JOIN callforwarddynamic cfwd ON n.pkid = cfwd.fknumplan',$this->cluster);

        // Create a line in the CSV for the Cluster heading
        Storage::append($this->outFile, $this->cluster->name . ',' . $this->cluster->ip);

        //If we got results.....
        if(!is_null($res))
        {
            //Check to see what moron forwarded their line to themselves
            foreach($res as $dn)
            {
                //If the right 10 digits of the dnorpattern matches the right 10 digits of the forward number....
                if(substr($dn->dnorpattern,-10) == substr($dn->cfadestination,-10))
                {
                    //Write this to the report
                    Storage::append($this->outFile,implode(",",[$dn->dnorpattern,$dn->description,$dn->cfadestination]));
                }
            }
        }
    }
}
