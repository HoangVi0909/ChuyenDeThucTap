<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Position;
use App\Models\SalaryRecord;

class SalaryDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật phụ cấp cho các chức vụ
        $positions = [
            'Quản lý' => 2000000,    // 2 triệu
            'Trưởng phòng' => 1500000, // 1.5 triệu  
            'Nhân viên' => 500000,    // 500k
            'Thực tập sinh' => 200000  // 200k
        ];

        foreach ($positions as $positionName => $allowance) {
            Position::where('name', $positionName)->update(['allowance' => $allowance]);
        }

        // Cập nhật lương theo giờ cho nhân viên
        $employees = Employee::with('position')->get();
        
        foreach ($employees as $employee) {
            $hourlyRate = 0;
            
            // Lương theo giờ dựa trên chức vụ
            switch ($employee->position->name) {
                case 'Quản lý':
                    $hourlyRate = 150000; // 150k/giờ
                    break;
                case 'Trưởng phòng':
                    $hourlyRate = 120000; // 120k/giờ
                    break;
                case 'Nhân viên':
                    $hourlyRate = 80000;  // 80k/giờ
                    break;
                case 'Thực tập sinh':
                    $hourlyRate = 50000;  // 50k/giờ
                    break;
                default:
                    $hourlyRate = 70000;  // Mặc định 70k/giờ
                    break;
            }
            
            $employee->update(['hourly_rate' => $hourlyRate]);
        }

        // Tạo bản ghi lương mẫu cho tháng hiện tại
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        
        // Tạo lương tháng trước (đã trả)
        $this->createSampleSalaryRecords($previousMonth, 'paid');
        
        // Tạo lương tháng hiện tại (chờ duyệt)
        $this->createSampleSalaryRecords($currentMonth, 'draft');
        
        $this->command->info('Đã cập nhật dữ liệu lương mẫu!');
    }

    private function createSampleSalaryRecords($monthYear, $status)
    {
        $employees = Employee::limit(5)->get(); // Tạo cho 5 nhân viên đầu tiên
        
        foreach ($employees as $employee) {
            // Kiểm tra xem đã có bản ghi chưa
            $existingRecord = SalaryRecord::where('employee_id', $employee->id)
                                         ->where('month_year', $monthYear)
                                         ->first();
            
            if (!$existingRecord) {
                SalaryRecord::create([
                    'employee_id' => $employee->id,
                    'month_year' => $monthYear,
                    'total_hours' => rand(160, 200), // 160-200 giờ/tháng
                    'hourly_rate' => $employee->hourly_rate,
                    'position_allowance' => $employee->position->allowance ?? 0,
                    'bonus' => $status === 'paid' ? rand(0, 1000000) : 0, // Thưởng ngẫu nhiên cho tháng trước
                    'penalty' => $status === 'paid' ? rand(0, 500000) : 0, // Phạt ngẫu nhiên cho tháng trước
                    'base_salary' => 0, // Sẽ được tính tự động
                    'total_salary' => 0, // Sẽ được tính tự động
                    'status' => $status,
                    'notes' => $status === 'paid' ? 'Đã trả lương tháng ' . $monthYear : 'Chờ duyệt lương tháng ' . $monthYear
                ]);
                
                // Tính toán lại lương
                $salaryRecord = SalaryRecord::where('employee_id', $employee->id)
                                           ->where('month_year', $monthYear)
                                           ->first();
                $salaryRecord->calculateSalary();
                $salaryRecord->save();
            }
        }
    }
}
