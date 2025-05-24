<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Thêm các trường mới sau trường status
            $table->enum('shift', ['morning', 'afternoon', 'full_day'])->default('morning')->after('status');
            $table->integer('workHours')->default(0)->after('shift');
            $table->date('check_day')->nullable()->after('workHours');
            $table->timestamp('check_in_time')->nullable()->after('check_day');
            $table->timestamp('check_out_time')->nullable()->after('check_in_time');
            $table->text('note')->nullable()->after('check_out_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Xóa các trường đã thêm
            $table->dropColumn([
                'shift',
                'workHours', 
                'check_day',
                'check_in_time',
                'check_out_time',
                'note'
            ]);
        });
    }
};