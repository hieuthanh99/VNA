<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ReportDate;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('daily:report')->everyMinute();  
        // $schedule->command('daily:report')->cron('0 16 * * 5');
        // $schedule->command('your:command')->cron('* * * * *');

        $reportDate = ReportDate::latest()->first();

        $reportDate = Carbon::parse($reportDate->report_date); 

        $reportDate->setTime(16, 00);

        $cronExpression = $reportDate->format('i H d m *');

        $schedule->command('daily:report')->cron($cronExpression);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
