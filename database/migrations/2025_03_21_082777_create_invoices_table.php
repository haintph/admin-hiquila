<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->unsignedBigInteger('table_id');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['Đang chuẩn bị', 'Đã phục vụ', 'Hoàn thành', 'Hủy đơn', 'Đã thanh toán'])->default('Đang chuẩn bị');
            $table->timestamps();

            $table->foreign('table_id')->references('table_id')->on('tables')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};

