<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id('detail_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('dish_id');
            $table->unsignedBigInteger('variant_id')->nullable(); // Thêm variant_id
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->timestamp('sent_to_kitchen_at')->nullable(); // Thêm cột tracking gửi bếp
            $table->timestamp('chef_confirmed_at')->nullable(); // Thêm cột tracking chef xác nhận
            $table->timestamps();
                                    
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices')->onDelete('cascade');
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            $table->foreign('variant_id')->references('id')->on('dish_variants')->onDelete('set null');
        });
    }
            
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
};