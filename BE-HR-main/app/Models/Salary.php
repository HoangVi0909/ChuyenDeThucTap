<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salary extends Model
{
    use HasFactory;
    
    protected $fillable = ['employee_id', 'amount', 'month', 'note'];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    // Format month để hiển thị
    public function getFormattedMonthAttribute()
    {
        return $this->month ? $this->month->format('Y-m') : '';
    }
}