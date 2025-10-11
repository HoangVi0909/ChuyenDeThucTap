<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeedbackController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        $token = session('employee_token');
        $apiUrl = config('app.be_api_url', 'http://127.0.0.1:8000');
        // Lấy thông tin employee hiện tại
        $employeeRes = Http::withToken($token)->get($apiUrl . '/api/employee/me');
        $employee = $employeeRes->json();
        if (!$employee || !isset($employee['id'])) {
            return back()->with('feedback_error', 'Không xác định được nhân viên!');
        }
        $response = Http::withToken($token)->post($apiUrl . '/api/employee/feedback', [
            'employee_id' => $employee['id'],
            'content' => $request->input('content'),
        ]);
        if ($response->successful()) {
            return back()->with('feedback_success', 'Gửi góp ý thành công!');
        } else {
            return back()->with('feedback_error', 'Gửi góp ý thất bại!');
        }
    }
}
