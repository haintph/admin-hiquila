<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    protected $fillable = [
        'role', 'base_salary', 'hourly_rate', 
        'required_hours_per_month', 'overtime_rate'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
    ];
}