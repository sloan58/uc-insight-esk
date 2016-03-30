<?php

namespace App\Console\Commands;

use App\Models\Duo\User;
use Illuminate\Console\Command;
use App\Jobs\GenerateRegisteredDuoUsersReport;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateRegisteredDuoUsersReportCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duo:user-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the Duo API User and Group Data';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        \Debugbar::disable();

        //Get a list of allDuo Users subscribed to the DuoRegisteredUsersReport
        $users = User::whereHas('reports', function ($query) {
            $query->where('name', 'DuoRegisteredUsersReport');
        })->get();

        //Loop each user to generate report
        foreach($users as $recipient)
        {
            $this->dispatch(new GenerateRegisteredDuoUsersReport($recipient));
//            \Log::debug('Message will be sent to:',[$recipient->email]);
        }
    }
}
