<?php
use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Notification;
use App\Models\Admin;
use Illuminate\Support\Carbon;

class DemoLeaveResignationNotificationSeeder extends Seeder
{
    public function run()
    {
        // Lấy 1 admin và 1 employee mẫu
        $admin = Admin::first();
        $employee = Employee::first();
        if (!$admin || !$employee) return;

        // Tạo đơn xin nghỉ phép mẫu
        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(4)->toDateString(),
            'type' => 'annual',
            'reason' => 'Nghỉ phép mẫu',
            'status' => 'pending',
        ]);

        // Tạo đơn xin nghỉ việc mẫu (nếu có model ResignationRequest)
        if (class_exists('App\\Models\\ResignationRequest')) {
            \App\Models\ResignationRequest::create([
                'employee_id' => $employee->id,
                'reason' => 'Muốn nghỉ việc mẫu',
                'status' => 'pending',
            ]);
        }

        // Tạo thông báo nội bộ cho admin và employee
        Notification::create([
            'user_id' => $admin->id,
            'user_type' => 'admin',
            'title' => 'Thông báo mẫu cho admin',
            'message' => 'Đây là thông báo nội bộ mẫu dành cho admin.',
            'type' => 'test',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $employee->id,
            'user_type' => 'employee',
            'title' => 'Thông báo mẫu cho nhân viên',
            'message' => 'Đây là thông báo nội bộ mẫu dành cho nhân viên.',
            'type' => 'test',
            'is_read' => false,
        ]);
    }
}
