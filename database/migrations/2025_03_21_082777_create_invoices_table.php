<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->unsignedBigInteger('table_id');
            
            // Thông tin khách hàng
            $table->string('customer_name')->nullable()->comment('Tên khách hàng');
            $table->string('customer_phone', 20)->nullable()->comment('Số điện thoại khách hàng');
            $table->integer('party_size')->nullable()->comment('Số lượng khách');
            $table->text('special_notes')->nullable()->comment('Ghi chú đặc biệt từ khách hàng');
            
            // Thông tin hóa đơn
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['Đang chuẩn bị', 'Đã phục vụ', 'Hoàn thành', 'Hủy đơn', 'Đã thanh toán'])->default('Đang chuẩn bị');
            $table->string('payment_method')->nullable()->comment('Phương thức thanh toán: cash, transfer, qr, vnpay, paypal');
            $table->timestamp('paid_at')->nullable()->comment('Thời gian thanh toán');
            $table->timestamp('sent_to_kitchen_at')->nullable()->comment('Thời gian gửi bếp');
            $table->timestamps();
            
            // Foreign key và indexes
            $table->foreign('table_id')->references('table_id')->on('tables')->onDelete('cascade');
            
            // Indexes cho tìm kiếm
            $table->index(['customer_phone']);
            $table->index(['customer_name']);
            $table->index(['status']);
            $table->index(['created_at']);
            $table->index(['paid_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};