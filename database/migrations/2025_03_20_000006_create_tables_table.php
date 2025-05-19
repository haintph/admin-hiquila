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
            $table->string('table_number', 10)->unique()->comment('Số bàn hiển thị');
            $table->integer('capacity')->comment('Sức chứa tối đa');
            
            // Loại bàn theo thực tế nhà hàng
            $table->enum('table_type', ['Bàn đôi', 'Bàn đơn', 'Bàn 4', 'Bàn 6', 'Bàn 8', 'Bàn dài', 'Bàn VIP','Bàn tròn'])
                  ->default('Bàn dài')
                  ->comment('Phân loại bàn');
                  
            // Trạng thái thực tế
            $table->enum('status', ['Trống', 'Đã đặt', 'Đang phục vụ', 'Đang dọn', 'Bảo trì'])
                  ->default('Trống')
                  ->comment('Trạng thái hiện tại của bàn');
                  
            // Khu vực - có thể tham chiếu đến bảng areas
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')
                  ->references('area_id')
                  ->on('areas')
                  ->onDelete('set null');
                  
            // Thông tin đặt bàn và phục vụ
            $table->unsignedBigInteger('current_order_id')->nullable()->comment('ID của đơn hàng hiện tại');
                  
            // Thời gian phục vụ
            $table->timestamp('occupied_at')->nullable()->comment('Thời gian bàn bắt đầu được sử dụng');
            
            // Thông tin bổ sung
            $table->integer('min_spend')->nullable()->comment('Chi tiêu tối thiểu cho bàn VIP');
            $table->text('notes')->nullable()->comment('Ghi chú về tình trạng bàn');
            $table->boolean('is_reservable')->default(true)->comment('Bàn có thể đặt trước');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Cho phép xóa mềm thay vì xóa hẳn bàn
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
}