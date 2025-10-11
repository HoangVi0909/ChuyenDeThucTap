<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Salary;
use App\Models\Employee;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        Salary::truncate();
        
        // Lấy tất cả employees
        $employees = Employee::all();
        
        if ($employees->count() == 0) {
            echo "Không có employee nào. Vui lòng chạy EmployeeSeeder trước.\n";
            return;
        }
        
        $salaryData = [];
        $months = ['2025-08-01', '2025-09-01', '2025-10-01'];
        
        foreach ($employees as $employee) {
            foreach ($months as $month) {
                $salaryData[] = [
                    'employee_id' => $employee->id,
                    'amount' => rand(5000000, 15000000), // 5M - 15M VNĐ
                    'month' => $month,
                    'note' => 'Lương tháng ' . date('m/Y', strtotime($month)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        // Insert dữ liệu
        Salary::insert($salaryData);
        
        echo "Đã tạo " . count($salaryData) . " bản ghi lương cho " . $employees->count() . " nhân viên.\n";
    }
}
