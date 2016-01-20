<?php

namespace App\Jobs;

use App\Libraries\Utils;
use App\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\DispatchesJobs;

class EraseTrustList extends Job implements SelfHandling, ShouldQueue
{

    use InteractsWithQueue,  DispatchesJobs, SerializesModels;

    private $eraserList;
    /**
     * @var Utils
     */
    private $utils;
    /**
     * @var \App\User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @param array $eraserList
     * @param \App\User $user
     * @internal param \App\Libraries\Utils $utils
     */
    public function __construct(Array $eraserList, User $user)
    {
        $this->eraserList = $eraserList;
        $this->utils = new Utils;
        $this->user = $user;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $formattedEraserList = $this->utils->generateEraserList($this->eraserList,$this->user);

        foreach($formattedEraserList as $device)
        {
            $this->dispatch(new ControlPhone($device,$this->user));
        }
    }
}
