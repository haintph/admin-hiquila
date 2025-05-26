<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $fillable = [
        'user_id', 'month', 'year', 'base_salary', 'hourly_salary',
        'overtime_salary', 'bonus', 'deduction', 'total_salary',
        'total_hours_worked', 'overtime_hours', 'days_worked', 'note'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'hourly_salary' => 'decimal:2',
        'overtime_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deduction' => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}