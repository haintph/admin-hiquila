<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id('table_id');
            $table->string('table_number', 10);
            $table->integer('capacity');
            $table->enum('status', ['Trống', 'Đã đặt', 'Đang phục vụ'])->default('Trống');
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')->references('area_id')->on('areas')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
}