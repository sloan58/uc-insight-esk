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
        $schedule->command('cucm:reports')
                 ->dailyAt('08:00');

        $schedule->command('duo:fetch-data')
                 ->dailyAt('07:00');

        $schedule->command('duo:fetch-data')
                 ->dailyAt('12:00');

        $schedule->command('duo:user-report')
                 ->dailyAt('08:00');

    }
}
