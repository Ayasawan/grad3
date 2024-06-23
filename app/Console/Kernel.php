<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\StatisticsService;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        
        // جدولة حساب الإحصاءات في بداية كل شهر عند الساعة 7 صباحًا
        // $schedule->call(function () {
        //     $controller = new StatisticController();
        //     $controller->calculateAndCacheMonthlyStatistics();
        // })->monthlyOn(1, '07:00');
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
