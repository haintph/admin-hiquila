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
        // Kiểm tra đến muộn mỗi 5 phút trong giờ hoạt động
        $schedule->command('tables:check-late')
                 ->everyFiveMinutes()
                 ->between('08:00', '23:59')
                 ->appendOutputTo(storage_path('logs/late-arrivals.log'));

        // Hoặc nếu muốn chạy 24/7:
        // $schedule->command('tables:check-late')->everyFiveMinutes();

        // Có thể thêm các schedule khác cho dự án:
        // $schedule->command('tables:cleanup-old-reservations')->daily();
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