<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'inventory_id', 'quantity', 'cost_per_unit'];

    public function purchase()
    {
        return $this->belongsTo(InventoryPurchase::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
