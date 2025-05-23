<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckLateArrivals extends Command
{
    protected $signature = 'tables:check-late {--minutes=15 : Minutes after reservation time to mark as late}';
    
    protected $description = 'Check for late table reservations and update status';

    public function handle()
    {
        $toleranceMinutes = $this->option('minutes');
        $lateThreshold = now()->subMinutes($toleranceMinutes);

        $this->info("Checking for table reservations late by more than {$toleranceMinutes} minutes...");

        // Tìm các bàn đã đặt nhưng quá giờ
        $lateTables = Table::where('status', 'Đã đặt')
            ->whereNotNull('reserved_time')
            ->where('reserved_time', '<', $lateThreshold)
            ->with('area')
            ->get();

        if ($lateTables->count() === 0) {
            $this->info('No late arrivals found.');
            return 0;
        }

        $updated = 0;
        foreach ($lateTables as $table) {
            $table->markAsLate();
            $updated++;
            
            $areaName = $table->area ? $table->area->name : 'Unknown Area';
            $this->line("Table {$table->table_number} ({$areaName}) marked as late");
            $this->line("  - Reserved by: {$table->reserved_by}");
            $this->line("  - Phone: {$table->reserved_phone}");
            $this->line("  - Reserved time: {$table->reserved_time->format('d/m/Y H:i')}");
            $this->line("  - Party size: {$table->reserved_party_size} guests");
            $this->line("");
        }

        $this->info("Updated {$updated} table(s) to 'Đến muộn' status.");
        
        // Log để theo dõi
        Log::info("CheckLateArrivals: Updated {$updated} tables to late status", [
            'tolerance_minutes' => $toleranceMinutes,
            'late_tables' => $lateTables->pluck('table_id')->toArray()
        ]);
        
        return 0;
    }
}

// Đăng ký trong app/Console/Kernel.php:
// protected function schedule(Schedule $schedule)
// {
//     $schedule->command('tables:check-late')->everyFiveMinutes();
//     
//     // Hoặc chạy mỗi phút để kiểm tra chính xác hơn
//     // $schedule->command('tables:check-late')->everyMinute();
//     
//     // Hoặc chỉ chạy trong giờ hoạt động nhà hàng
//     // $schedule->command('tables:check-late')
//     //          ->everyFiveMinutes()
//     //          ->between('08:00', '23:00');
// }