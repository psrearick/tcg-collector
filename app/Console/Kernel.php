<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands() : void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule) : void
    {
        $schedule->command('import:prices')
            ->dailyAt('4:00');
        $schedule->command('summaries:update')
            ->dailyAt('6:00');
        $schedule->command('import:cards')
            ->weeklyOn(6, '6:00');
        $schedule->command('import:symbols')
            ->weeklyOn(6, '9:00');
        $schedule->command('generate:apimap')
            ->weeklyOn(6, '10:00');
        $schedule->command('import:sets')
            ->weeklyOn(6, '5:30');
    }
}
