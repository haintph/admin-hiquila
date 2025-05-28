<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dish_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dish_id')->constrained('dishes')->onDelete('cascade');
            $table->string('name'); // Tên biến thể: Size L, Tôm hùm loại A
            $table->decimal('price', 10, 2)->comment('Giá theo đơn vị (vd: kg, phần)');
            $table->string('unit')->default('phần'); // Đơn vị: kg, con, phần, chai...
            $table->integer('stock')->default(0)->comment('Số lượng tồn kho (nếu có)');
            $table->boolean('is_available')->default(true); // Còn bán hay không
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dish_variants');
    }
};
