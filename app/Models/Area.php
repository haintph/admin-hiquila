<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas'; // Tên bảng
    protected $primaryKey = 'area_id';

    public $timestamps = true; // Sử dụng timestamps
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
    public function tables()
    {
        return $this->hasMany(Table::class, 'area_id', 'area_id');
    }
}
