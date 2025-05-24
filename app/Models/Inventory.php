<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'quantity',
        'min_quantity',
        'cost_per_unit',
        'description'
    ];

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function purchases()
    {
        return $this->hasMany(InventoryPurchaseDetail::class);
    }

    public function usages()
    {
        return $this->hasMany(InventoryUsage::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
