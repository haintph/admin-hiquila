<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatus extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'is_online', 'last_seen'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
