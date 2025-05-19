<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $fillable = [
        'inventory_id', 'user_id', 'type', 'quantity', 'cost', 'note'
    ];

    // Quan hệ với bảng inventories
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    // Quan hệ với bảng users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



