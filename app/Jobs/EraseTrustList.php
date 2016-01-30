<?php

namespace App\Jobs;

use App\Models\Cluster;
use App\Libraries\Utils;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

/**
 * Class EraseTrustList
 * @package App\Jobs
 */
class EraseTrustList extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,  DispatchesJobs, SerializesModels;

    /**
     * @var array
     */
    private $eraserList;

    /**
     * @var Cluster
     */
    private $cluster;

    /**
     * @param array $eraserList
     * @param Cluster $cluster
     */
    public function __construct(Array $eraserList, Cluster $cluster)
    {
        $this->eraserList = $eraserList;
        $this->cluster = $cluster;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $formattedEraserList = Utils::generateEraserList($this->eraserList,$this->cluster);

        foreach($formattedEraserList as $device)
        {
            $this->dispatch(new ControlPhone($device,$this->cluster));
        }
    }
}
