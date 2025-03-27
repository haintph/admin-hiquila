<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    protected $primaryKey = 'area_id';
    protected $fillable = [
        'name',
        'description',
        'status',
        'capacity',
        'floor',
        'is_smoking',
        'is_vip',
        'surcharge',
        'image'
    ];
}
