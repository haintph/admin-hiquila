<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        'is_reservable',
        // Thông tin đặt bàn
        'reserved_by',
        'reserved_phone',
        'reserved_time',
        'reserved_party_size',
        'reservation_notes',
        'reserved_at'
    ];

    protected $dates = [
        'occupied_at',
        'reserved_time',
        'reserved_at',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'capacity' => 'integer',
        'min_spend' => 'integer',
        'reserved_party_size' => 'integer',
        'is_reservable' => 'boolean',
        'occupied_at' => 'datetime',
        'reserved_time' => 'datetime',
        'reserved_at' => 'datetime',
    ];

    /**
     * Quan hệ với khu vực
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }

    /**
     * Quan hệ với hóa đơn - một bàn có thể có nhiều hóa đơn
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'table_id', 'table_id');
    }

    /**
     * Lấy hóa đơn hiện tại (đang hoạt động)
     */
    public function currentInvoice()
    {
        return $this->hasOne(Invoice::class, 'table_id', 'table_id')
                    ->whereNotIn('status', ['Hủy đơn', 'Đã thanh toán'])
                    ->latest();
    }

    /**
     * Kiểm tra xem bàn có hóa đơn đang hoạt động không
     */
    public function hasActiveInvoice()
    {
        return $this->currentInvoice()->exists();
    }

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
     * Kiểm tra có đặt bàn không
     */
    public function hasReservation()
    {
        return !is_null($this->reserved_by) && in_array($this->status, ['Đã đặt', 'Đến muộn']);
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
     * Lấy thông tin đặt bàn đầy đủ
     */
    public function getReservationInfoAttribute()
    {
        if (!$this->hasReservation()) {
            return null;
        }

        return [
            'customer_name' => $this->reserved_by,
            'customer_phone' => $this->reserved_phone,
            'reservation_time' => $this->reserved_time,
            'party_size' => $this->reserved_party_size,
            'notes' => $this->reservation_notes,
            'reserved_at' => $this->reserved_at,
            'status' => $this->status
        ];
    }

    /**
     * Kiểm tra đến muộn
     */
    public function isLate($toleranceMinutes = 15)
    {
        if (!$this->reserved_time || $this->status !== 'Đã đặt') {
            return false;
        }

        return now()->diffInMinutes($this->reserved_time, false) > $toleranceMinutes;
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
        return $query->whereIn('status', ['Đã đặt', 'Đến muộn']);
    }

    /**
     * Scope để lấy đặt bàn theo số điện thoại
     */
    public function scopeReservedByPhone($query, $phone)
    {
        return $query->where('reserved_phone', $phone)
                    ->whereIn('status', ['Đã đặt', 'Đến muộn']);
    }

    /**
     * Scope để lấy đặt bàn theo tên
     */
    public function scopeReservedByName($query, $name)
    {
        return $query->where('reserved_by', $name)
                    ->whereIn('status', ['Đã đặt', 'Đến muộn']);
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

    /**
     * Methods để quản lý đặt bàn
     */
    public function makeReservation($customerName, $customerPhone, $reservedTime, $partySize, $notes = null)
    {
        return $this->update([
            'status' => 'Đã đặt',
            'reserved_by' => $customerName,
            'reserved_phone' => $customerPhone,
            'reserved_time' => $reservedTime,
            'reserved_party_size' => $partySize,
            'reservation_notes' => $notes,
            'reserved_at' => now()
        ]);
    }

    /**
     * Hủy đặt bàn
     */
    public function cancelReservation()
    {
        return $this->update([
            'status' => 'Trống',
            'reserved_by' => null,
            'reserved_phone' => null,
            'reserved_time' => null,
            'reserved_party_size' => null,
            'reservation_notes' => null,
            'reserved_at' => null
        ]);
    }

    /**
     * Check-in khách hàng - CẬP NHẬT ĐỂ TẠO HÓA ĐƠN
     */
    public function checkIn()
    {
        // Bắt đầu transaction để đảm bảo tính nhất quán
        DB::beginTransaction();

        try {
            // Cập nhật trạng thái bàn
            $this->update([
                'status' => 'Đang phục vụ',
                'occupied_at' => now()
            ]);

            // Tạo hóa đơn mới nếu chưa có hóa đơn đang hoạt động
            if (!$this->hasActiveInvoice()) {
                $invoice = Invoice::create([
                    'table_id' => $this->table_id,
                    'total_price' => 0,
                    'status' => 'Đang chuẩn bị'
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Đánh dấu đến muộn
     */
    public function markAsLate()
    {
        return $this->update([
            'status' => 'Đến muộn'
        ]);
    }

    /**
     * Scope để lấy đặt bàn của khách hàng
     */
    public function scopeByCustomer($query, $phone, $name = null)
    {
        return $query->where(function($q) use ($phone, $name) {
            $q->where('reserved_phone', $phone);
            if ($name) {
                $q->orWhere('reserved_by', $name);
            }
        })->whereIn('status', ['Đã đặt', 'Đến muộn']);
    }

    /**
     * Lấy thông tin đặt bàn cho view
     */
    public function getReservationForDisplayAttribute()
    {
        if (!$this->hasReservation()) {
            return null;
        }

        return [
            'customer_name' => $this->reserved_by,
            'customer_phone' => $this->reserved_phone,
            'reservation_time_formatted' => $this->reserved_time ? $this->reserved_time->format('d/m/Y H:i') : '',
            'party_size' => $this->reserved_party_size,
            'notes' => $this->reservation_notes,
            'status' => $this->status,
            'table_number' => $this->table_number,
            'area_name' => $this->area->name ?? 'Không xác định'
        ];
    }

    /**
     * Hoàn thành phục vụ và dọn bàn
     */
    public function completeService()
    {
        DB::beginTransaction();

        try {
            // Đánh dấu hóa đơn hiện tại là hoàn thành (nếu có)
            $currentInvoice = $this->currentInvoice()->first();
            if ($currentInvoice) {
                $currentInvoice->complete();
            }

            // Reset trạng thái bàn
            $this->update([
                'status' => 'Trống',
                'occupied_at' => null,
                'reserved_by' => null,
                'reserved_phone' => null,
                'reserved_time' => null,
                'reserved_party_size' => null,
                'reservation_notes' => null,
                'reserved_at' => null
            ]);

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}