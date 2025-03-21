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
            $table->string('name'); // Tên biến thể (vd: Size L, Thêm phô mai...)
            $table->decimal('price', 10, 2); // Giá của biến thể
            $table->integer('stock')->default(0); // Số lượng tồn kho
            $table->boolean('is_available')->default(true); // Có bán hay không
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dish_variants');
    }
};
