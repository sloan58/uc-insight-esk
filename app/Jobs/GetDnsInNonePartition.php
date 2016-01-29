<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Cluster;
use App\Models\Sql;
use Carbon\Carbon;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Storage;

class GetDnsInNonePartition extends Job implements SelfHandling
{
    /**
     * @var \App\Models\Sql
     */
    private $sql;
    /**
     * @var \App\Models\Cluster
     */
    private $cluster;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->sql = new Sql();
        $this->cluster = new Cluster();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $clusters = $this->cluster->all();

        $unassignedDnReport = [];
        foreach($clusters as $cluster)
        {
            $res = $this->sql->executeQuery('SELECT dnorpattern, description FROM numplan WHERE fkroutepartition IS NULL AND tkpatternusage = 2',$cluster);

            $unassignedDnReport[$cluster->name] = $res;
        }


        $filename = 'reports/nonePt/CucmNonePartition-' . Carbon::now()->timestamp .'.csv';

        Storage::put($filename,'Directory Number,Description');

        foreach($unassignedDnReport as $cluster => $data)
        {
            Storage::append($filename, $cluster . ',');

            foreach($data as $dn)
            {
                Storage::append($filename,$dn->dnorpattern . ',' . $dn->description);

            }
        }

        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.welcome', [], function($message) use($filename)
        {
            $message
                ->from('info@laireight.com')
                ->to('martinsloan58@gmail.com', 'Marty Sloan')
                ->subject('CUCM None Partition Report')
                ->attach(storage_path("app/$filename"));
        });

    }
}
