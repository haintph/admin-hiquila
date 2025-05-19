<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'table_id';
    
    protected $fillable = [
        'table_number', 
        'capacity', 
        'table_type',
        'status', 
        'area_id',
        'current_order_id',
        'occupied_at',
        'min_spend',
        'notes',
        'is_reservable'
    ];

    protected $dates = [
        'occupied_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'min_spend' => 'integer',
        'is_reservable' => 'boolean',
    ];

    /**
     * Quan hệ với khu vực
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    /**
     * Quan hệ với đơn hàng hiện tại
     */

    /**
     * Kiểm tra xem bàn có đang sử dụng không
     */
    public function isOccupied()
    {
        return $this->status === 'Đang phục vụ' || $this->status === 'Đã đặt';
    }

    /**
     * Kiểm tra xem bàn có thể đặt không
     */
    public function isAvailableForReservation()
    {
        return $this->is_reservable && $this->status === 'Trống';
    }

    /**
     * Tính thời gian đã sử dụng bàn (phút)
     */
    public function getOccupiedTimeAttribute()
    {
        if (!$this->occupied_at) {
            return 0;
        }
        
        return now()->diffInMinutes($this->occupied_at);
    }

    /**
     * Lấy thời gian bàn đã được sử dụng theo định dạng giờ:phút
     */
    public function getFormattedOccupiedTimeAttribute()
    {
        $minutes = $this->occupied_time;
        
        if ($minutes === 0) {
            return '-';
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        return sprintf('%02d:%02d', $hours, $mins);
    }

    /**
     * Scope để lấy các bàn đang trống
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Trống');
    }

    /**
     * Scope để lấy các bàn đang phục vụ
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', 'Đang phục vụ');
    }

    /**
     * Scope để lấy các bàn đã đặt
     */
    public function scopeReserved($query)
    {
        return $query->where('status', 'Đã đặt');
    }

    /**
     * Scope để lấy các bàn theo loại bàn
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('table_type', $type);
    }

    /**
     * Scope để lấy các bàn theo sức chứa tối thiểu
     */
    public function scopeWithMinCapacity($query, $capacity)
    {
        return $query->where('capacity', '>=', $capacity);
    }
}