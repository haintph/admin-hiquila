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
            
            // Thông tin cơ bản
            $table->string('code', 10)->unique()->comment('Mã khu vực (A, B, C)'); 
            $table->string('name', 100)->comment('Tên khu vực');
            $table->text('description')->nullable()->comment('Mô tả khu vực');
            
            // Trạng thái khu vực
            $table->enum('status', ['Hoạt động', 'Bảo trì', 'Đóng cửa'])
                  ->default('Hoạt động')
                  ->comment('Trạng thái mặc định khu vực');
        
            // Thông tin bố trí
            $table->integer('floor')->nullable()->comment('Tầng');
            $table->integer('capacity')->nullable()->comment('Sức chứa tối đa người');
            
            // Đặc điểm khu vực
            $table->boolean('is_smoking')->default(false)->comment('Khu vực hút thuốc');
            $table->boolean('is_vip')->default(false)->comment('Khu vực VIP');
            $table->decimal('surcharge', 10, 2)->default(0)->comment('Phụ phí khu vực');
            
            // Hình ảnh và bố trí
            $table->string('image')->nullable()->comment('Hình ảnh khu vực');
            $table->text('layout_data')->nullable()->comment('Dữ liệu bố trí bàn (có thể lưu dạng JSON)');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // Cho phép xóa mềm thay vì xóa hẳn khu vực
        });
    }

    public function down()
    {
        Schema::dropIfExists('areas');
    }
}