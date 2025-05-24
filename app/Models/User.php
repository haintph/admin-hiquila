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
        'shift',           // Thêm trường shift
        'workHours',       // Thêm trường workHours
        'check_day',       // Thêm trường check_day
        'check_in_time',   // Thêm trường check_in_time
        'check_out_time',  // Thêm trường check_out_time
        'note'             // Thêm trường note
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'dob' => 'date',                    // Cast dob thành date
        'hire_date' => 'date',              // Cast hire_date thành date
        'check_day' => 'date',              // Cast check_day thành date
        'check_in_time' => 'datetime',      // Cast check_in_time thành datetime
        'check_out_time' => 'datetime',     // Cast check_out_time thành datetime
        'workHours' => 'integer',           // Cast workHours thành integer
        'salary' => 'decimal:2',            // Cast salary thành decimal với 2 chữ số thập phân
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

    // Thêm các accessor/mutator nếu cần
    public function getShiftLabelAttribute()
    {
        return match($this->shift) {
            'morning' => 'Ca sáng',
            'afternoon' => 'Ca chiều', 
            'full_day' => 'Cả ngày',
            default => 'Không xác định'
        };
    }

    public function getFormattedWorkHoursAttribute()
    {
        return $this->workHours . ' giờ';
    }

    // Scope để lọc theo ca làm việc
    public function scopeByShift($query, $shift)
    {
        return $query->where('shift', $shift);
    }

    // Scope để lọc theo ngày check-in
    public function scopeByCheckDay($query, $date)
    {
        return $query->whereDate('check_day', $date);
    }
}