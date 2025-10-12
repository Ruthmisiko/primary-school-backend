<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\SendParentSmsJob;
use App\Models\StudentParent;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

    $schedule->job(new \App\Jobs\SendParentSmsJob)->everyMinute();
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
