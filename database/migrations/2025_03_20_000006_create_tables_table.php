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
            $table->string('table_number', 10)->comment('Số bàn hiển thị (A1, A2, B1, B2...)');
            $table->integer('capacity')->comment('Sức chứa tối đa (1-20 người)');

            // Loại bàn theo thực tế nhà hàng
            $table->enum('table_type', ['Bàn đơn', 'Bàn đôi', 'Bàn 4', 'Bàn 6', 'Bàn 8', 'Bàn dài', 'Bàn VIP', 'Bàn tròn'])
                ->default('Bàn dài')
                ->comment('Phân loại bàn');

            // Trạng thái thực tế
            $table->enum('status', ['Trống', 'Đã đặt', 'Đang phục vụ', 'Đã thanh toán', 'Đang dọn', 'Bảo trì', 'Không hoạt động', 'Đến muộn'])
                ->default('Trống')
                ->comment('Trạng thái hiện tại của bàn');

            // Khu vực - tham chiếu đến bảng areas với unique constraint mới
            $table->unsignedBigInteger('area_id')->nullable();
            $table->foreign('area_id')
                ->references('area_id')
                ->on('areas')
                ->onDelete('set null')
                ->comment('Khu vực của bàn (A, B, C theo tầng)');

            // Thông tin đặt bàn và phục vụ
            $table->unsignedBigInteger('current_order_id')->nullable()->comment('ID của đơn hàng hiện tại');

            // Thời gian phục vụ
            $table->timestamp('occupied_at')->nullable()->comment('Thời gian bàn bắt đầu được sử dụng');

            // ===== THÔNG TIN ĐẶT BÀN =====
            // Thông tin khách hàng đặt bàn
            $table->string('reserved_by')->nullable()->comment('Tên người đặt bàn');
            $table->string('reserved_phone', 20)->nullable()->comment('Số điện thoại người đặt');

            // Thông tin thời gian đặt bàn
            $table->timestamp('reserved_time')->nullable()->comment('Thời gian đặt bàn');
            $table->integer('reserved_party_size')->nullable()->comment('Số lượng khách đặt bàn');
            $table->text('reservation_notes')->nullable()->comment('Ghi chú đặt bàn');
            $table->timestamp('reserved_at')->nullable()->comment('Thời gian thực hiện đặt bàn');

            // Thông tin bổ sung
            $table->integer('min_spend')->nullable()->comment('Chi tiêu tối thiểu cho bàn VIP');
            $table->text('notes')->nullable()->comment('Ghi chú về tình trạng bàn');
            $table->boolean('is_reservable')->default(true)->comment('Bàn có thể đặt trước');

            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Cho phép xóa mềm thay vì xóa hẳn bàn

            // Index để tối ưu truy vấn
            $table->index(['area_id', 'status']);
            $table->index(['status']);
            $table->index(['table_number']);

            // Index cho đặt bàn
            $table->index(['reserved_phone']);
            $table->index(['reserved_time']);
            $table->index(['status', 'reserved_time']);

            // Unique constraint: table_number + area_id (cho phép cùng số bàn ở khác khu vực)
            $table->unique(['table_number', 'area_id'], 'tables_number_area_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
}
