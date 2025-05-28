<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'purchase_date', 'total_cost', 'note'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(InventoryPurchaseDetail::class);
    }
}
