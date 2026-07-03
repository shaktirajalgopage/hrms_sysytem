<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('employees:check-birthdays')->dailyAt('00:12');
        $schedule->command('attendance:auto-checkout')
        ->dailyAt('22:00')
        ->timezone('Asia/Kolkata')
        ->withoutOverlapping()
        ->onOneServer()
        ->appendOutputTo(storage_path('logs/auto-checkout.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
