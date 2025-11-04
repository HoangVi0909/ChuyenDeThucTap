<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('employee_token')) {
            return redirect('/employee/login');
        }
        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        $employee = Http::withToken($token)->get($baseUrl . '/api/employee/me')->json() ?? [];
        $notifications = Http::withToken($token)->get($baseUrl . '/api/employee/notifications')->json();
        if (isset($notifications['data'])) {
            $notifications = $notifications['data'];
        }
        $feedbacks = Http::withToken($token)->get($baseUrl . '/api/feedback')->json();
        if (isset($feedbacks['data'])) {
            $feedbacks = $feedbacks['data'];
        }
        // Lấy lịch làm việc của nhân viên hiện tại
        $work_schedules = [];
        $work_schedules_count = 0;
        if (!empty($employee['id'])) {
            $wsRes = Http::withToken($token)->get($baseUrl . '/api/employee/my-work-schedules?employee_id=' . $employee['id'])->json();
            if (isset($wsRes['data'])) {
                $work_schedules = $wsRes['data'];
            } elseif (is_array($wsRes)) {
                $work_schedules = $wsRes;
            }
            $work_schedules_count = is_array($work_schedules) ? count($work_schedules) : 0;
        }
        $notifications_count = is_array($notifications) ? count($notifications) : 0;
        return view('employee.dashboard', compact('employee', 'notifications', 'feedbacks', 'notifications_count', 'work_schedules', 'work_schedules_count'));
    }

    public function workSchedule(Request $request)
    {
        if (!session('employee_token')) {
            return redirect('/employee/login');
        }
        
        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');
        
        // Lấy thông tin nhân viên hiện tại
        $employee = Http::withToken($token)->get($baseUrl . '/api/employee/me')->json() ?? [];
        
        if (empty($employee['id'])) {
            return redirect('/employee/dashboard')->with('error', 'Không thể xác định thông tin nhân viên');
        }
        
        // Xác định tuần hiện tại hoặc tuần được chọn
        $weekStart = $request->get('week_start');
        if (!$weekStart) {
            $weekStart = now()->startOfWeek()->format('Y-m-d');
        } else {
            $weekStart = date('Y-m-d', strtotime($weekStart));
        }
        
        // Tạo mảng các ngày trong tuần
        $weekDays = [];
        $dayNames = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
        
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime($weekStart . " +{$i} days"));
            $weekDays[] = [
                'dayName' => $dayNames[$i],
                'date' => date('d/m/Y', strtotime($date)),
                'dateFormat' => $date
            ];
        }
        
        $weekEnd = date('d/m/Y', strtotime($weekStart . ' +6 days'));
        $weekStartDisplay = date('d/m/Y', strtotime($weekStart));
        
        // Lấy tham số filter từ request
        $filterShift = $request->get('shift');
        $filterDateFrom = $request->get('date_from');
        $filterDateTo = $request->get('date_to');
        
        // Nếu có filter ngày, sử dụng thay vì tuần
        if ($filterDateFrom && $filterDateTo) {
            $startDate = $filterDateFrom;
            $endDate = $filterDateTo;
        } else {
            // Sử dụng tuần như cũ
            $startDate = $weekStart;
            $endDate = date('Y-m-d', strtotime($weekStart . ' +6 days'));
        }
        
        // Lấy lịch làm việc của nhân viên
        $workSchedules = [];
        
        try {
            $apiParams = [
                'date_from' => $startDate,
                'date_to' => $endDate
            ];
            
            // Thêm filter ca làm việc nếu có
            if ($filterShift) {
                $apiParams['shift'] = $filterShift;
            }
            
            $response = Http::withToken($token)->get($baseUrl . '/api/employee/my-work-schedules', $apiParams);
            
            // Debug logging
            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Body: ' . $response->body());
            
            if ($response->successful()) {
                $data = $response->json();
                $workSchedules = isset($data['data']) ? $data['data'] : $data;
                Log::info('Work Schedules Count: ' . count($workSchedules));
            } else {
                Log::error('API call failed with status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception in work schedule API call: ' . $e->getMessage());
            $workSchedules = [];
        }
        
        return view('employee.work_schedule', [
            'weekDays' => $weekDays,
            'weekStart' => $weekStartDisplay,
            'weekEnd' => $weekEnd,
            'currentWeekStart' => $weekStart,
            'workSchedules' => $workSchedules,
            'employee' => $employee,
            'token' => $token
        ]);
    }

    public function resignation()
    {
        if (!session('employee_token')) {
            return redirect('/employee/login');
        }

        $token = session('employee_token');
        return view('employee.resignation', compact('token'));
    }
}