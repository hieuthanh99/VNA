<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\ReportDate;
use Carbon\Carbon;
use App\Models\ReportCenter;
use DateTimeZone;
use DateTime;

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
        $date = $reportDate->report_date;
        $carbonDate = Carbon::parse($date);
        $carbonDate->setTime(16, 00);
        $tz_from = 'Asia/Ho_Chi_Minh'; 

        $newDateTime = new DateTime($carbonDate, new DateTimeZone($tz_from)); 

        $newDateTime->setTimezone(new DateTimeZone("UTC"));
        
        $dateTimeUTC = $newDateTime->format("Y-m-d H:i:s");
        $hour = Carbon::parse($dateTimeUTC)->hour;
        $minute = Carbon::parse($dateTimeUTC)->minute;

        $dayOfWeek = $carbonDate->dayOfWeek;
        $cronExpression = "$minute $hour * * $dayOfWeek";

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
