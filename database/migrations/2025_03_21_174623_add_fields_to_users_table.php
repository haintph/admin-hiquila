<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('other');
            $table->string('avatar', 255)->nullable();
            $table->decimal('salary', 10, 2)->default(0);
            $table->date('hire_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn(['phone', 'address', 'dob', 'gender', 'avatar', 'salary', 'hire_date', 'status']);
        });
    }
};
