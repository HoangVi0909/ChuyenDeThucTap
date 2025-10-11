<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        $token = session('admin_token');
        try {
            $employees = Http::withToken($token)->get($apiUrl . '/api/admin/employees')->json() ?? [];
            $departments = Http::withToken($token)->get($apiUrl . '/api/admin/departments')->json() ?? [];
            $positions = Http::withToken($token)->get($apiUrl . '/api/admin/positions')->json() ?? [];
            $notifications = Http::withToken($token)->get($apiUrl . '/api/admin/notifications')->json() ?? [];
            $workSchedules = Http::withToken($token)->get($apiUrl . '/api/admin/work-schedules')->json() ?? [];
            $stats = [
                'employees_count' => count($employees),
                'active_employees' => count(array_filter($employees, function($emp) { 
                    return isset($emp['status']) && $emp['status'] == 'active'; 
                })),
                'departments_count' => count($departments),
                'positions_count' => count($positions),
                'notifications_count' => isset($notifications['total']) ? $notifications['total'] : count($notifications),
                'total_notifications' => isset($notifications['total']) ? $notifications['total'] : count($notifications),
                'total_work_schedules' => isset($workSchedules['total']) ? $workSchedules['total'] : count($workSchedules),
            ];
        } catch (\Exception $e) {
            $stats = [
                'employees_count' => 0,
                'active_employees' => 0,
                'departments_count' => 0,
                'positions_count' => 0,
                'notifications_count' => 0,
                'total_notifications' => 0,
                'total_work_schedules' => 0,
            ];
        }
        return view('admin.dashboard', compact('stats'));
    }
}
