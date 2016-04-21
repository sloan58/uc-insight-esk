<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CallManagerReportingCommand::class,
        Commands\FetchDuoApiDataCommand::class,
        Commands\GenerateRegisteredDuoUsersReportCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //Run the CUCM Report Command
        $schedule->command('cucm:reports')
                 ->dailyAt('08:00');

        //Fetch Duo User data
        $schedule->command('duo:fetch-data')
                ->hourly();

        //Generate Duo User report
        $schedule->command('duo:user-report')
                 ->weekly()->mondays()->at('08:00');

    }
}
