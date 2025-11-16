<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        if (!session('employee_token')) {
            return redirect()->route('employee.login');
        }

        $token = session('employee_token');
        $baseUrl = config('services.backend_api.url');

        $workSchedules = [];
        $employee = null;

        try {
            // Lấy thông tin nhân viên
            $employeeResponse = Http::withToken($token)->get("{$baseUrl}/api/employee/me");
            if ($employeeResponse->successful()) {
                $empData = $employeeResponse->json();
                $employee = $empData['data'] ?? $empData;
            }

            // Tuần hiện tại hoặc tuần được chọn
            $weekStart = $request->input('week_start') 
                ? date('Y-m-d', strtotime($request->input('week_start')))
                : now()->startOfWeek()->toDateString();
            $weekEnd = date('Y-m-d', strtotime("$weekStart +6 days"));

            // Tạo danh sách ngày trong tuần
            $weekDays = [];
            for ($i = 0; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("$weekStart +$i days"));
                $weekDays[] = [
                    'date' => $date,
                    'dateFormat' => $date,
                ];
            }

            // Lấy lịch làm việc của tuần hiện tại
            $wsResponse = Http::withToken($token)->get("{$baseUrl}/api/employee/my-work-schedules", [
                'date_from' => $weekStart,
                'date_to' => $weekEnd
            ]);

            if ($wsResponse->successful()) {
                $wsData = $wsResponse->json();
                if (isset($wsData['data'])) {
                    $workSchedules = collect($wsData['data'])->map(function($item){
                        $item['work_date'] = date('Y-m-d', strtotime($item['work_date']));
                        return $item;
                    })->toArray();
                }
            }

        } catch (\Exception $e) {
            Log::error('Employee work schedule error: ' . $e->getMessage());
            $workSchedules = [];
        }

        return view('employee.work_schedule', compact(
            'employee', 'workSchedules', 'weekDays', 'weekStart', 'weekEnd'
        ));
    }
}
