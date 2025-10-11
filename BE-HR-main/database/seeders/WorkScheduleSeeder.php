<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkSchedule;
use App\Models\Employee;
use Carbon\Carbon;

class WorkScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ
        WorkSchedule::truncate();
        
        // Lấy tất cả employees
        $employees = Employee::all();
        
        if ($employees->count() == 0) {
            echo "Không có employee nào. Vui lòng chạy EmployeeSeeder trước.\n";
            return;
        }
        
        $scheduleData = [];
        $shifts = ['S', 'C']; // Ca sáng và ca chiều
        
        // Tạo lịch làm việc cho 30 ngày tiếp theo
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);
        
        foreach ($employees as $employee) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Bỏ qua cuối tuần (thứ 7, chủ nhật)
                if (!$currentDate->isWeekend()) {
                    // Random ca làm việc
                    $shift = $shifts[array_rand($shifts)];
                    
                    $scheduleData[] = [
                        'employee_id' => $employee->id,
                        'work_date' => $currentDate->format('Y-m-d'),
                        'shift' => $shift,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                $currentDate->addDay();
            }
        }
        
        // Insert dữ liệu theo batch để tối ưu performance
        $chunks = array_chunk($scheduleData, 100);
        foreach ($chunks as $chunk) {
            WorkSchedule::insert($chunk);
        }
        
        echo "Đã tạo " . count($scheduleData) . " lịch làm việc cho " . $employees->count() . " nhân viên.\n";
    }
}
