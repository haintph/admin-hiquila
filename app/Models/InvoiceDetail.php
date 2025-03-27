<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'detail_id';
    protected $fillable = ['invoice_id', 'dish_id', 'quantity', 'price'];

    public function dish()
    {
        return $this->belongsTo(Dish::class, 'dish_id', 'id');
    }
}
