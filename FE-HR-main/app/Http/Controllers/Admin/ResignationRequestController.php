<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ResignationRequestController extends Controller
{
    public function index(Request $request)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }

        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');

        // Lấy danh sách đơn xin nghỉ việc
        $apiParams = [];
        if ($request->has('status') && $request->status) {
            $apiParams['status'] = $request->status;
        }
        if ($request->has('employee_search') && $request->employee_search) {
            $apiParams['employee_search'] = $request->employee_search;
        }

        try {
            $response = Http::withToken($token)->get($baseUrl . '/api/admin/resignation-requests', $apiParams);
            
            if ($response->successful()) {
                $data = $response->json();
                $resignationRequests = $data['data'] ?? [];
                $pagination = [
                    'current_page' => $data['current_page'] ?? 1,
                    'last_page' => $data['last_page'] ?? 1,
                    'total' => $data['total'] ?? 0
                ];
            } else {
                $resignationRequests = [];
                $pagination = ['current_page' => 1, 'last_page' => 1, 'total' => 0];
            }
        } catch (\Exception $e) {
            $resignationRequests = [];
            $pagination = ['current_page' => 1, 'last_page' => 1, 'total' => 0];
        }

        return view('admin.resignation_requests.index', compact('resignationRequests', 'pagination'));
    }

    public function show($id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }

        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');

        try {
            $response = Http::withToken($token)->get($baseUrl . "/api/admin/resignation-requests/{$id}");
            
            if ($response->successful()) {
                $resignationRequest = $response->json();
                return view('admin.resignation_requests.show', compact('resignationRequest'));
            } else {
                return redirect()->route('admin.resignation-requests.index')
                    ->with('error', 'Không tìm thấy đơn xin nghỉ việc');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.resignation-requests.index')
                ->with('error', 'Có lỗi xảy ra khi tải thông tin đơn');
        }
    }

    public function updateStatus(Request $request, $id)
    {
        if (!session('admin_token')) {
            return redirect('/admin/login');
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500'
        ]);

        $token = session('admin_token');
        $baseUrl = config('services.backend_api.url');

        try {
            $response = Http::withToken($token)->patch($baseUrl . "/api/admin/resignation-requests/{$id}/status", [
                'status' => $request->status,
                'admin_note' => $request->admin_note
            ]);
            
            if ($response->successful()) {
                $statusText = $request->status === 'approved' ? 'duyệt' : 'từ chối';
                return redirect()->back()->with('success', "Đã {$statusText} đơn xin nghỉ việc thành công");
            } else {
                $error = $response->json();
                return redirect()->back()->with('error', $error['message'] ?? 'Có lỗi xảy ra');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý đơn');
        }
    }
}
