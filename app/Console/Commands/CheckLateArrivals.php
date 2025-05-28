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

        $this->info("Checking for table reservations late by more than {$toleranceMinutes} minutes...");
        $this->info("Current time: " . now()->format('Y-m-d H:i:s'));

        // FIXED: Tính toán thời gian đúng cách
        // Tìm các bàn có reserved_time + tolerance < hiện tại
        $lateThreshold = now()->subMinutes($toleranceMinutes);

        $lateTables = Table::where('status', 'Đã đặt')
            ->whereNotNull('reserved_time')
            ->whereNotNull('reserved_by')
            ->where('reserved_time', '<', $lateThreshold)
            ->with('area')
            ->get();

        $this->info("Late threshold: {$lateThreshold->format('Y-m-d H:i:s')}");
        $this->info("Found {$lateTables->count()} potentially late tables");

        if ($lateTables->count() === 0) {
            $this->info('No late arrivals found.');
            
            // Debug info
            $allReserved = Table::where('status', 'Đã đặt')
                ->whereNotNull('reserved_time')
                ->whereNotNull('reserved_by')
                ->get();
                
            $this->info("Debug - Total reserved tables: {$allReserved->count()}");
            foreach ($allReserved as $table) {
                $reservedTime = Carbon::parse($table->reserved_time);
                $minutesAgo = now()->diffInMinutes($reservedTime);
                $this->line("  - Table {$table->table_number}: {$reservedTime->format('H:i')} ({$minutesAgo} minutes ago)");
            }
            
            return 0;
        }

        $updated = 0;
        foreach ($lateTables as $table) {
            try {
                $table->markAsLate();
                $updated++;
                
                $areaName = $table->area ? $table->area->name : 'Unknown Area';
                $reservedTime = Carbon::parse($table->reserved_time);
                $minutesLate = now()->diffInMinutes($reservedTime);
                
                $this->line("✓ Table {$table->table_number} ({$areaName}) marked as late");
                $this->line("  - Reserved by: {$table->reserved_by}");
                $this->line("  - Phone: {$table->reserved_phone}");
                $this->line("  - Reserved time: {$reservedTime->format('d/m/Y H:i')}");
                $this->line("  - Minutes late: {$minutesLate}");
                $this->line("  - Party size: {$table->reserved_party_size} guests");
                $this->line("");
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to update Table {$table->table_number}: " . $e->getMessage());
            }
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