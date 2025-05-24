<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'detail_id';
    
    protected $fillable = [
        'invoice_id', 
        'dish_id', 
        'variant_id', 
        'quantity', 
        'price',
        'sent_to_kitchen_at'  // ⭐ THÊM DÒNG NÀY
    ];

    // Thêm casts để xử lý datetime
    protected $casts = [
        'sent_to_kitchen_at' => 'datetime',
        'price' => 'decimal:2'
    ];

    public function dish()
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }
         
    public function variant()
    {
        return $this->belongsTo(DishVariant::class, 'variant_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }
}