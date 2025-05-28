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
        // Kiểm tra đến muộn mỗi 5 phút - chạy 24/7
        $schedule->command('tables:check-late')
                 ->everyFiveMinutes()
                 ->appendOutputTo(storage_path('logs/late-arrivals.log'));

        // Tự động hủy đặt bàn quá hạn mỗi 30 phút - chạy 24/7  
        $schedule->command('reservations:auto-cancel --hours=2')
                 ->everyThirtyMinutes()
                 ->appendOutputTo(storage_path('logs/auto-cancel.log'));
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