<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalaryRecord extends Model
{
    use HasFactory;


    protected $fillable = [
        'employee_id',
        'month_year',
        'total_hours',
        'hourly_rate',
        'position_allowance',
        'bonus',
        'penalty',
        'base_salary',
        'total_salary',
        'tax_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'total_hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'bonus' => 'decimal:2',
        'penalty' => 'decimal:2',
        'base_salary' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    // Quan hệ với Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Tính toán lương tự động (có thuế)
    public function calculateSalary()
    {
        // Ép kiểu float để tính toán
        $base_salary = (float) $this->total_hours * (float) $this->hourly_rate;
        $position_allowance = (float) $this->position_allowance;
        $bonus = (float) $this->bonus;
        $penalty = (float) $this->penalty;
        $gross = $base_salary + $position_allowance + $bonus - $penalty;
        $tax = 0;
        if ($gross > 15000000) {
            $tax = round($gross * 0.05, 2);
        }
    $this->base_salary = floatval($base_salary);
    $this->tax_amount = floatval($tax);
    $this->total_salary = floatval($gross - $tax);
        return $this;
    }

    // Tính số giờ làm từ work_schedules
    public function calculateHoursFromSchedules()
    {
        if (!$this->employee_id || !$this->month_year) {
            return 0;
        }

        // Parse month_year (format: 2025-10)
        $date = Carbon::createFromFormat('Y-m', $this->month_year);
        $startDate = $date->startOfMonth()->format('Y-m-d');
        $endDate = $date->endOfMonth()->format('Y-m-d');

        // Lấy tất cả lịch làm việc trong tháng
        $workSchedules = WorkSchedule::where('employee_id', $this->employee_id)
            ->whereBetween('work_date', [$startDate, $endDate])
            ->where('status', 'completed') // Chỉ tính những ca đã hoàn thành
            ->get();

        $totalHours = 0;
        foreach ($workSchedules as $schedule) {
            if ($schedule->start_time && $schedule->end_time) {
                $start = Carbon::parse($schedule->start_time);
                $end = Carbon::parse($schedule->end_time);
                $totalHours += $end->diffInHours($start);
            }
        }

        $this->total_hours = $totalHours;
        return $totalHours;
    }

    // Tạo hoặc cập nhật bản ghi lương cho tháng
    public static function createOrUpdateForMonth($employeeId, $monthYear)
    {
        $employee = Employee::with('position')->find($employeeId);
        if (!$employee) {
            return null;
        }

        $salaryRecord = self::firstOrNew([
            'employee_id' => $employeeId,
            'month_year' => $monthYear
        ]);

        // Tính số giờ làm từ lịch
        $salaryRecord->calculateHoursFromSchedules();
        
        // Lấy lương theo giờ từ employee
        $salaryRecord->hourly_rate = $employee->hourly_rate ?? 0;
        
        // Lấy phụ cấp chức vụ (có thể từ position hoặc cấu hình riêng)
        $salaryRecord->position_allowance = $employee->position->allowance ?? 0;
        
        // Tính toán lương
        $salaryRecord->calculateSalary();
        
        $salaryRecord->save();
        
        return $salaryRecord;
    }

    // Scope để lọc theo tháng
    public function scopeForMonth($query, $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }

    // Scope để lọc theo trạng thái
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
