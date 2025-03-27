<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $primaryKey = 'table_id';
    protected $fillable = ['table_number', 'capacity', 'status', 'area_id'];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id', 'area_id');
    }
}
