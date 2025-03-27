<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
        public function up()
        {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('shift', ['morning', 'night', 'full_day'])->default('morning')->after('status');
            });
        }
    
        public function down()
        {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('shift');
            });
        }
    };
    

