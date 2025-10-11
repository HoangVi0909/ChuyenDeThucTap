<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkSchedule extends Model
{
    use HasFactory;
    
    protected $fillable = ['employee_id', 'work_date', 'shift'];
    
    protected $casts = [
        'work_date' => 'date',
    ];
    
    // Định nghĩa các ca làm việc
    const SHIFTS = [
        'S' => 'Ca sáng (8:00 - 17:00)',
        'C' => 'Ca chiều (14:00 - 23:00)',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    // Accessor để lấy tên ca làm việc
    public function getShiftNameAttribute()
    {
        return self::SHIFTS[$this->shift] ?? 'Không xác định';
    }
    
    // Scope để lọc theo tháng
    public function scopeInMonth($query, $month, $year)
    {
        return $query->whereMonth('work_date', $month)
                    ->whereYear('work_date', $year);
    }
    
    // Scope để lọc theo tuần
    public function scopeInWeek($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }
}
