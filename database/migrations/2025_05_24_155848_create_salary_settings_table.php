<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySettingsTable extends Migration
{
    public function up()
    {
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // manager, staff, chef, cashier
            $table->decimal('base_salary', 10, 2)->default(0); // Lương cơ bản/tháng
            $table->decimal('hourly_rate', 8, 2)->default(0); // Lương theo giờ
            $table->integer('required_hours_per_month')->default(160); // Giờ chuẩn/tháng
            $table->decimal('overtime_rate', 8, 2)->default(0); // Lương tăng ca (1.5x hourly_rate)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_settings');
    }
}