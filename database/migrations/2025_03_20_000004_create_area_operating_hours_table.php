<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAreaOperatingHoursTable extends Migration
{
    public function up()
    {
        // Tạo bảng khung giờ hoạt động
        Schema::create('area_operating_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id')->comment('ID của khu vực');
            $table->time('start_time')->comment('Giờ bắt đầu');
            $table->time('end_time')->comment('Giờ kết thúc');
            $table->boolean('is_active')->default(true)->comment('Trạng thái kích hoạt');
            $table->integer('display_order')->default(0)->comment('Thứ tự hiển thị');
            $table->timestamps();

            $table->foreign('area_id')
                ->references('area_id')
                ->on('areas')
                ->onDelete('cascade');
        });

        // Bảng cấu hình giờ hoạt động
        Schema::create('area_hour_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('area_id')->unique()->comment('ID của khu vực');
            $table->boolean('has_operating_hours')->default(false)
                ->comment('Khu vực có khung giờ hoạt động riêng không');
            $table->enum('non_operating_status', ['Bảo trì', 'Đóng cửa'])
                ->default('Đóng cửa')
                ->comment('Trạng thái khi ngoài giờ hoạt động');
            $table->timestamps();

            $table->foreign('area_id')
                ->references('area_id')
                ->on('areas')
                ->onDelete('cascade');
        });

        // Xóa cột nếu tồn tại
        Schema::table('areas', function (Blueprint $table) {
            if (Schema::hasColumn('areas', 'has_operating_hours')) {
                $table->dropColumn('has_operating_hours');
            }
            if (Schema::hasColumn('areas', 'operating_hours')) {
                $table->dropColumn('operating_hours');
            }
            if (Schema::hasColumn('areas', 'non_operating_status')) {
                $table->dropColumn('non_operating_status');
            }
        });
    }

    public function down()
    {
        // Xóa bảng con trước
        Schema::dropIfExists('area_operating_hours');
        Schema::dropIfExists('area_hour_settings');

        // Thêm lại các cột vào bảng areas
        Schema::table('areas', function (Blueprint $table) {
            if (!Schema::hasColumn('areas', 'has_operating_hours')) {
                $table->boolean('has_operating_hours')->default(false)
                    ->comment('Khu vực có khung giờ hoạt động riêng không');
            }
            if (!Schema::hasColumn('areas', 'operating_hours')) {
                $table->json('operating_hours')->nullable()
                    ->comment('Danh sách các khung giờ hoạt động (JSON)');
            }
            if (!Schema::hasColumn('areas', 'non_operating_status')) {
                $table->enum('non_operating_status', ['Bảo trì', 'Đóng cửa'])
                    ->default('Đóng cửa')
                    ->comment('Trạng thái khi ngoài giờ hoạt động');
            }
        });
    }
}
