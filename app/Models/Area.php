<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'area_id';

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'floor',
        'capacity',
        'is_smoking',
        'is_vip',
        'surcharge',
        'image',
        'layout_data'
    ];

    // Đảm bảo status có thể gán giá trị
    protected $attributes = [
        'status' => 'Hoạt động'
    ];

    protected $casts = [
        'is_smoking' => 'boolean',
        'is_vip' => 'boolean',
        'surcharge' => 'decimal:2',
        'floor' => 'integer',
        'capacity' => 'integer',
        'layout_data' => 'json'
    ];

    /**
     * Lấy bàn trong khu vực
     */
    public function tables()
    {
        return $this->hasMany(Table::class, 'area_id', 'area_id');
    }

    /**
     * Lấy cài đặt giờ hoạt động của khu vực
     */
    public function hourSetting()
    {
        return $this->hasOne(AreaHourSetting::class, 'area_id', 'area_id');
    }
    /**
     * Boot model - thêm các sự kiện lifecycle
     */
    protected static function boot()
    {
        parent::boot();

        // Lắng nghe sự kiện khi cập nhật khu vực
        static::updated(function ($area) {
            // Nếu trạng thái đã thay đổi
            if ($area->isDirty('status')) {
                $area->syncTableStatuses();
            }
        });
    }

    /**
     * Đồng bộ trạng thái của các bàn theo trạng thái khu vực
     */
    public function syncTableStatuses()
    {
        // Lấy setting giờ hoạt động
        $hourSetting = $this->hourSetting;

        // Nếu khu vực đang đóng cửa (hoặc trạng thái ngoài giờ hoạt động)
        if (
            $hourSetting &&
            ($this->status == 'Đóng cửa' || $this->status == $hourSetting->non_operating_status)
        ) {

            // Cập nhật tất cả bàn không đang được sử dụng sang trạng thái "Không hoạt động"
            DB::table('tables')
                ->where('area_id', $this->area_id)
                ->whereNotIn('status', ['Đã đặt', 'Đang phục vụ', 'Bảo trì'])
                ->update(['status' => 'Không hoạt động']);
        }
        // Nếu khu vực đang hoạt động
        else if ($this->status == 'Hoạt động') {
            // Cập nhật tất cả bàn "Không hoạt động" thành "Trống"
            DB::table('tables')
                ->where('area_id', $this->area_id)
                ->where('status', 'Không hoạt động')
                ->update(['status' => 'Trống']);
        }

        return true;
    }

    /**
     * Thủ công cập nhật trạng thái khu vực và đồng bộ trạng thái bàn
     */
    public function updateStatusAndSyncTables($newStatus)
    {
        $this->status = $newStatus;
        $this->save();
        // Đảm bảo syncTableStatuses được gọi sau khi lưu
        $this->syncTableStatuses();

        return true;
    }
    /**
     * Lấy các khung giờ hoạt động của khu vực
     */
    public function operatingHours()
    {
        return $this->hasMany(AreaOperatingHour::class, 'area_id', 'area_id')
            ->orderBy('display_order')
            ->orderBy('start_time');
    }

    /**
     * Kiểm tra xem thời gian hiện tại có nằm trong khung giờ hoạt động không
     * 
     * @param Carbon|null $currentTime Thời gian cần kiểm tra (mặc định là thời gian hiện tại)
     * @return bool True nếu trong giờ hoạt động, False nếu ngoài giờ hoạt động
     */
    public function isWithinOperatingHours(Carbon $currentTime = null)
    {
        // Kiểm tra xem khu vực có cài đặt giờ hoạt động không
        if (!$this->relationLoaded('hourSetting')) {
            $this->load('hourSetting');
        }

        if (!$this->hourSetting || !$this->hourSetting->has_operating_hours) {
            return true; // Luôn trong giờ hoạt động nếu không có cài đặt
        }

        // Lấy thời gian hiện tại nếu không được cung cấp
        if (!$currentTime) {
            $currentTime = Carbon::now();
        }

        // Chỉ lấy giờ và phút để so sánh
        $currentTimeStr = $currentTime->format('H:i:s');

        // Kiểm tra xem có nằm trong bất kỳ khung giờ hoạt động nào không
        if (!$this->relationLoaded('operatingHours')) {
            $this->load('operatingHours');
        }

        foreach ($this->operatingHours as $operatingHour) {
            if (!$operatingHour->is_active) continue;

            $startTime = $operatingHour->start_time;
            $endTime = $operatingHour->end_time;

            // Kiểm tra thời gian hiện tại có nằm trong khoảng này không
            if ($currentTimeStr >= $startTime && $currentTimeStr <= $endTime) {
                return true;
            }
        }

        return false; // Không nằm trong bất kỳ khung giờ hoạt động nào
    }

    /**
     * Trạng thái hiện tại của khu vực, có tính tới giờ hoạt động
     */
    public function getCurrentStatusAttribute()
    {
        // Kiểm tra xem có cài đặt giờ hoạt động không
        if (!$this->relationLoaded('hourSetting')) {
            $this->load('hourSetting');
        }

        // Nếu không có cài đặt giờ hoạt động, trả về trạng thái mặc định
        if (!$this->hourSetting || !$this->hourSetting->has_operating_hours) {
            return $this->status;
        }

        // Kiểm tra xem có đang trong giờ hoạt động không
        if ($this->isWithinOperatingHours()) {
            return $this->status; // Trong giờ hoạt động, trả về trạng thái mặc định
        } else {
            // Ngoài giờ hoạt động, trả về trạng thái theo cài đặt
            return $this->hourSetting->non_operating_status;
        }
    }
    public function getRouteKeyName()
    {
        return 'area_id';
    }
}
