<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'phone',
        'address',
        'dob',
        'gender',
        'avatar',
        'salary',
        'hire_date',
        'status',
        'note',
        'shift'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->orWhere('receiver_id', $this->id);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function friends()
    {
        return $this->hasMany(Friendship::class, 'user_id')->where('status', 'accepted');
    }

    public function status()
    {
        return $this->hasOne(UserStatus::class);
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-' . $this->id);
    }
}
