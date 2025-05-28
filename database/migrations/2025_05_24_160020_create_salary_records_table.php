<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('month'); // 1-12
            $table->integer('year'); // 2025, 2026...
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('hourly_salary', 10, 2)->default(0);
            $table->decimal('overtime_salary', 10, 2)->default(0);
            $table->decimal('bonus', 10, 2)->default(0);
            $table->decimal('deduction', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2)->default(0);
            $table->integer('total_hours_worked')->default(0);
            $table->integer('overtime_hours')->default(0);
            $table->integer('days_worked')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('salary_records');
    }
}