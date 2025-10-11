<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('employee_token')) {
            return redirect('/employee/login');
        }
        $token = session('employee_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $employee = Http::withToken($token)->get($apiUrl.'/api/employee/me')->json() ?? [];
        $notifications = Http::withToken($token)->get($apiUrl.'/api/employee/notifications')->json();
        if (isset($notifications['data'])) {
            $notifications = $notifications['data'];
        }
        $feedbacks = Http::withToken($token)->get($apiUrl.'/api/feedback')->json();
        if (isset($feedbacks['data'])) {
            $feedbacks = $feedbacks['data'];
        }
        // Lấy lịch làm việc của nhân viên hiện tại
        $work_schedules = [];
        $work_schedules_count = 0;
        if (!empty($employee['id'])) {
            $wsRes = Http::withToken($token)->get($apiUrl.'/api/employee/my-work-schedules?employee_id='.$employee['id'])->json();
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
}
