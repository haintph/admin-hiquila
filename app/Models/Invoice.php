<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $primaryKey = 'invoice_id';

    protected $fillable = [
        'table_id',
        'customer_name',        // THÊM MỚI
        'customer_phone',       // THÊM MỚI
        'party_size',          // THÊM MỚI
        'special_notes',       // THÊM MỚI
        'total_price',
        'status',
        'payment_method',
        'paid_at',
        'sent_to_kitchen_at'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'sent_to_kitchen_at' => 'datetime',
        'paid_at' => 'datetime',
        'party_size' => 'integer'  // THÊM MỚI
    ];

    /**
     * Relationship với bàn
     */
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'table_id');
    }

    /**
     * Relationship với chi tiết hóa đơn
     */
    public function items()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id', 'invoice_id');
    }

    /**
     * Alias cho items() để dễ hiểu hơn
     */
    public function details()
    {
        return $this->items();
    }

    /**
     * Tính tổng tiền từ chi tiết hóa đơn
     */
    public function calculateTotal()
    {
        $total = $this->items()->sum(DB::raw('quantity * price'));
        $this->update(['total_price' => $total]);
        return $total;
    }

    /**
     * Gửi đơn xuống bếp
     */
    public function sendToKitchen()
    {
        $this->update([
            'status' => 'Đã phục vụ',
            'sent_to_kitchen_at' => now()
        ]);
    }

    /**
     * Hoàn thành hóa đơn
     */
    public function complete()
    {
        $this->update(['status' => 'Hoàn thành']);
    }

    /**
     * Thanh toán hóa đơn với phương thức cụ thể
     */
    public function markAsPaid($paymentMethod = null)
    {
        $this->update([
            'status' => 'Đã thanh toán',
            'payment_method' => $paymentMethod,
            'paid_at' => now()
        ]);
    }

    /**
     * Hủy hóa đơn
     */
    public function cancel()
    {
        $this->update(['status' => 'Hủy đơn']);
    }

    /**
     * Scope để lấy hóa đơn đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['Hủy đơn', 'Đã thanh toán']);
    }

    /**
     * Scope để lấy hóa đơn hôm nay
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope để lấy hóa đơn theo trạng thái
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope để lấy hóa đơn trong khoảng thời gian
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // THÊM MỚI - Scope để tìm kiếm theo thông tin khách hàng
    public function scopeSearchCustomer($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }

    /**
     * Accessor để hiển thị trạng thái với màu sắc cho Bootstrap
     */
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'Đang chuẩn bị' => 'warning',
            'Đã phục vụ' => 'info',
            'Hoàn thành' => 'success',
            'Đã thanh toán' => 'primary',
            'Hủy đơn' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Accessor để lấy tên trạng thái hiển thị
     */
    public function getStatusDisplayAttribute()
    {
        return match ($this->status) {
            'Đang chuẩn bị' => 'Đang chuẩn bị',
            'Đã phục vụ' => 'Đã phục vụ',
            'Hoàn thành' => 'Hoàn thành',
            'Đã thanh toán' => 'Đã thanh toán',
            'Hủy đơn' => 'Hủy đơn',
            default => 'Không xác định'
        };
    }

    /**
     * Accessor để lấy tên phương thức thanh toán
     */
    public function getPaymentMethodNameAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'Tiền mặt',
            'transfer' => 'Chuyển khoản',
            'qr' => 'QR Code',
            'vnpay' => 'VNPAY',
            'paypal' => 'PayPal',
            default => 'Chưa xác định'
        };
    }

    /**
     * Accessor để lấy icon phương thức thanh toán
     */
    public function getPaymentMethodIconAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'solar:wallet-money-bold-duotone',
            'transfer' => 'solar:card-transfer-bold-duotone',
            'qr' => 'solar:qr-code-bold-duotone',
            'vnpay' => 'solar:card-bold-duotone',
            'paypal' => 'solar:card-bold-duotone',
            default => 'solar:question-circle-bold-duotone'
        };
    }

    /**
     * Accessor để lấy màu badge phương thức thanh toán
     */
    public function getPaymentMethodColorAttribute()
    {
        return match ($this->payment_method) {
            'cash' => 'success',
            'transfer' => 'info',
            'qr' => 'warning',
            'vnpay' => 'primary',
            'paypal' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Accessor để định dạng tiền tệ
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_price, 0, ',', '.') . ' đ';
    }

    // THÊM MỚI - Accessor cho thông tin khách hàng đầy đủ
    public function getCustomerInfoAttribute()
    {
        $info = [];
        
        if ($this->customer_name) {
            $info[] = $this->customer_name;
        }
        
        if ($this->customer_phone) {
            $info[] = $this->customer_phone;
        }
        
        if ($this->party_size) {
            $info[] = $this->party_size . ' khách';
        }
        
        return !empty($info) ? implode(' - ', $info) : 'Khách lẻ';
    }

    /**
     * Kiểm tra xem hóa đơn có thể chỉnh sửa không
     */
    public function canEdit()
    {
        return in_array($this->status, ['Đang chuẩn bị', 'Đã phục vụ']);
    }

    /**
     * Kiểm tra xem hóa đơn có thể hủy không
     */
    public function canCancel()
    {
        return in_array($this->status, ['Đang chuẩn bị', 'Đã phục vụ']);
    }

    /**
     * Kiểm tra xem hóa đơn có thể thanh toán không
     */
    public function canPay()
    {
        return $this->status === 'Đã phục vụ' && $this->total_price > 0;
    }

    /**
     * Thêm món vào hóa đơn
     */
    public function addItem($productId, $quantity, $unitPrice, $notes = null)
    {
        // Kiểm tra xem món đã có trong hóa đơn chưa
        $existingItem = $this->items()->where('dish_id', $productId)->first(); // Sửa product_id thành dish_id

        if ($existingItem) {
            // Nếu đã có, cập nhật số lượng
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'notes' => $notes ?? $existingItem->notes
            ]);
            $item = $existingItem;
        } else {
            // Nếu chưa có, tạo mới
            $item = $this->items()->create([
                'dish_id' => $productId, // Sửa product_id thành dish_id
                'quantity' => $quantity,
                'price' => $unitPrice,   // Sửa unit_price thành price
                'notes' => $notes
            ]);
        }

        // Cập nhật tổng tiền
        $this->calculateTotal();

        return $item;
    }

    /**
     * Xóa món khỏi hóa đơn
     */
    public function removeItem($itemId)
    {
        $item = $this->items()->find($itemId);
        if ($item) {
            $item->delete();
            $this->calculateTotal();
            return true;
        }
        return false;
    }

    /**
     * Cập nhật số lượng món
     */
    public function updateItemQuantity($itemId, $quantity)
    {
        $item = $this->items()->find($itemId);
        if ($item) {
            if ($quantity <= 0) {
                $item->delete();
            } else {
                $item->update(['quantity' => $quantity]);
            }
            $this->calculateTotal();
            return true;
        }
        return false;
    }

    /**
     * Lấy thông tin đầy đủ cho hiển thị
     */
    public function getDisplayInfoAttribute()
    {
        return [
            'invoice_id' => $this->invoice_id,
            'table_number' => $this->table->table_number ?? 'N/A',
            'area_name' => $this->table->area->name ?? 'N/A',
            'customer_name' => $this->customer_name ?? 'Khách lẻ',        // THÊM MỚI
            'customer_phone' => $this->customer_phone,                    // THÊM MỚI
            'party_size' => $this->party_size,                          // THÊM MỚI
            'special_notes' => $this->special_notes,                    // THÊM MỚI
            'customer_info_display' => $this->customer_info,            // THÊM MỚI
            'total_formatted' => $this->formatted_total,
            'status' => $this->status_display,
            'status_color' => $this->status_color,
            'payment_method' => $this->payment_method_name,
            'payment_method_color' => $this->payment_method_color,
            'payment_method_icon' => $this->payment_method_icon,
            'created_at_formatted' => $this->created_at->format('d/m/Y H:i'),
            'paid_at_formatted' => $this->paid_at ? $this->paid_at->format('d/m/Y H:i') : null,
            'sent_to_kitchen_formatted' => $this->sent_to_kitchen_at ? $this->sent_to_kitchen_at->format('d/m/Y H:i') : null,
            'items_count' => $this->items()->count(),
            'can_edit' => $this->canEdit(),
            'can_cancel' => $this->canCancel(),
            'can_pay' => $this->canPay()
        ];
    }
}