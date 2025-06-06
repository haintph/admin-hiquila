<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dish_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->onDelete('cascade');
            $table->string('image_path'); // Đường dẫn ảnh
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dish_images');
    }
};
