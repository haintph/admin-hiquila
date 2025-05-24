<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DishVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'dish_id',
        'name',
        'price',
        'unit',
        'stock',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock' => 'integer',
    ];

    /**
     * Mối quan hệ với Dish.
     */
    public function dish(): BelongsTo
    {
        return $this->belongsTo(Dish::class);
    }

    /**
     * Accessor để hiển thị giá với định dạng tiền tệ
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . 'đ';
    }

    /**
     * Accessor để hiển thị trạng thái dưới dạng text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_available ? 'Có sẵn' : 'Hết hàng';
    }

    /**
     * Scope để lọc các biến thể có sẵn
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope để lọc các biến thể hết hàng
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('is_available', false);
    }
}