<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->call('App\Http\Controllers\Cron\MessageController@run')->everyMinute();
        $schedule->call('App\Http\Controllers\Cron\ScheduleController@run')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
