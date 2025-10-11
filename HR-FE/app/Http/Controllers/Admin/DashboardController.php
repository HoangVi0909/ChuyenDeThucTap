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
        $baseUrl = config('services.backend_api.url');
        $token = session('admin_token');
        try {
            $employees = Http::withToken($token)->get($baseUrl . '/api/admin/employees')->json() ?? [];
            $departments = Http::withToken($token)->get($baseUrl . '/api/admin/departments')->json() ?? [];
            $positions = Http::withToken($token)->get($baseUrl . '/api/admin/positions')->json() ?? [];
            $notifications = Http::withToken($token)->get($baseUrl . '/api/admin/notifications')->json() ?? [];
            $workSchedules = Http::withToken($token)->get($baseUrl . '/api/admin/work-schedules')->json() ?? [];
            $stats = [
                'employees_count' => count($employees),
                'departments_count' => count($departments),
                'positions_count' => count($positions),
                'notifications_count' => isset($notifications['total']) ? $notifications['total'] : count($notifications),
                'total_work_schedules' => isset($workSchedules['total']) ? $workSchedules['total'] : count($workSchedules),
            ];
        } catch (\Exception $e) {
            $stats = [
                'employees_count' => 0,
                'departments_count' => 0,
                'positions_count' => 0,
                'notifications_count' => 0,
                'total_work_schedules' => 0,
            ];
        }
        return view('admin.dashboard', compact('stats'));
    }
}
