<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreasTable extends Migration
{
    public function up()
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id('area_id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('status', ['Hoạt động', 'Bảo trì', 'Đóng cửa'])->default('Hoạt động');
            $table->integer('capacity')->nullable();
            $table->integer('floor')->nullable();
            $table->boolean('is_smoking')->default(false);
            $table->boolean('is_vip')->default(false);
            $table->decimal('surcharge', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('areas');
    }
}