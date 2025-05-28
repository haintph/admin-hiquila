<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Logo extends Model
{
    use HasFactory;

    protected $fillable = ['image'];

    // Lấy URL đầy đủ của logo
    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }

    // Xóa file khi xóa record
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($logo) {
            if (Storage::exists('public/' . $logo->image)) {
                Storage::delete('public/' . $logo->image);
            }
        });
    }
}