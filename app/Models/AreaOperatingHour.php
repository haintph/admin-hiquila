<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaOperatingHour extends Model
{
    use HasFactory;
    protected $fillable = [
        'area_id',
        'start_time',
        'end_time',
        'is_active',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Lấy khu vực liên quan
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    /**
     * Lấy cài đặt giờ của khu vực
     */
    public function hourSetting()
    {
        return $this->belongsTo(AreaHourSetting::class, 'area_id', 'area_id');
    }
}
