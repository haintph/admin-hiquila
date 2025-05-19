<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaHourSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'area_id',
        'has_operating_hours',
        'non_operating_status'
    ];

    protected $casts = [
        'has_operating_hours' => 'boolean',
    ];

    /**
     * Lấy khu vực liên quan
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    /**
     * Lấy các khung giờ hoạt động của khu vực
     */
    public function operatingHours()
    {
        return $this->hasMany(AreaOperatingHour::class, 'area_id', 'area_id');
    }
}
