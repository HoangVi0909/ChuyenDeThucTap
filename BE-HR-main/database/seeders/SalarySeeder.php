<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalaryRecord;
use App\Models\Employee;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        SalaryRecord::truncate();
        
        // Lấy tất cả employees
        $employees = Employee::all();
        
        if ($employees->count() == 0) {
            echo "Không có employee nào. Vui lòng chạy EmployeeSeeder trước.\n";
            return;
        }
        
        $salaryData = [];
        $months = ['2025-08', '2025-09', '2025-10'];
        
        foreach ($employees as $employee) {
            foreach ($months as $month) {
                $totalHours = rand(160, 200); // 160-200 giờ/tháng
                $hourlyRate = rand(50000, 100000); // 50k-100k VNĐ/giờ
                $positionAllowance = rand(500000, 2000000); // 500k-2M phụ cấp
                $bonus = rand(0, 1000000); // 0-1M thưởng
                $penalty = rand(0, 200000); // 0-200k phạt
                
                $baseSalary = $totalHours * $hourlyRate;
                $totalSalary = $baseSalary + $positionAllowance + $bonus - $penalty;
                
                $salaryData[] = [
                    'employee_id' => $employee->id,
                    'month_year' => $month,
                    'total_hours' => $totalHours,
                    'hourly_rate' => $hourlyRate,
                    'base_salary' => $baseSalary,
                    'position_allowance' => $positionAllowance,
                    'bonus' => $bonus,
                    'penalty' => $penalty,
                    'total_salary' => $totalSalary,
                    'status' => ['draft', 'approved', 'paid'][rand(0, 2)],
                    'notes' => 'Lương tháng ' . $month,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert dữ liệu
        SalaryRecord::insert($salaryData);
        
        echo "Đã tạo " . count($salaryData) . " bản ghi lương cho " . $employees->count() . " nhân viên.\n";
    }
}