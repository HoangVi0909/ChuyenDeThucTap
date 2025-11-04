<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResignationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResignationRequestController extends Controller
{
    /**
     * Nhân viên tạo đơn xin nghỉ việc
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|min:10|max:1000',
            'expected_resignation_date' => 'required|date|after:today'
        ], [
            'reason.required' => 'Vui lòng nhập lý do nghỉ việc',
            'reason.min' => 'Lý do nghỉ việc phải có ít nhất 10 ký tự',
            'reason.max' => 'Lý do nghỉ việc không được quá 1000 ký tự',
            'expected_resignation_date.required' => 'Vui lòng chọn ngày nghỉ dự kiến',
            'expected_resignation_date.date' => 'Ngày nghỉ dự kiến không hợp lệ',
            'expected_resignation_date.after' => 'Ngày nghỉ dự kiến phải sau ngày hôm nay'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra xem nhân viên đã có đơn đang chờ duyệt chưa
        $existingRequest = ResignationRequest::where('employee_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return response()->json([
                'message' => 'Bạn đã có đơn xin nghỉ việc đang chờ duyệt'
            ], 400);
        }

        try {
            $resignationRequest = ResignationRequest::create([
                'employee_id' => $user->id,
                'reason' => $request->reason,
                'expected_resignation_date' => $request->expected_resignation_date,
                'status' => 'pending'
            ]);

            return response()->json([
                'message' => 'Đã gửi đơn xin nghỉ việc thành công',
                'data' => $resignationRequest->load('employee')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi gửi đơn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách đơn xin nghỉ việc của nhân viên hiện tại
     */
    public function myRequests(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        try {
            $requests = ResignationRequest::where('employee_id', $user->id)
                ->with(['employee', 'reviewer'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requests, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể tải danh sách đơn xin nghỉ việc',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin lấy tất cả đơn xin nghỉ việc
     */
    public function index(Request $request)
    {
        try {
            $query = ResignationRequest::with(['employee', 'reviewer'])
                ->orderBy('created_at', 'desc');

            // Lọc theo trạng thái nếu có
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            // Lọc theo nhân viên nếu có
            if ($request->has('employee_search') && $request->employee_search) {
                $query->whereHas('employee', function($q) use ($request) {
                    $search = $request->employee_search;
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $requests = $query->paginate(15);

            return response()->json($requests, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể tải danh sách đơn xin nghỉ việc',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin duyệt hoặc từ chối đơn xin nghỉ việc
     */
    public function updateStatus(Request $request, $id)
    {
        $admin = $request->user();
        if (!$admin) {
            return response()->json(['message' => 'Chưa đăng nhập'], 401);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string|max:500'
        ], [
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
            'admin_note.max' => 'Ghi chú không được quá 500 ký tự'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $resignationRequest = ResignationRequest::find($id);
            
            if (!$resignationRequest) {
                return response()->json(['message' => 'Không tìm thấy đơn xin nghỉ việc'], 404);
            }

            if ($resignationRequest->status !== 'pending') {
                return response()->json(['message' => 'Đơn này đã được xử lý'], 400);
            }

            $resignationRequest->update([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now()
            ]);

            return response()->json([
                'message' => $request->status === 'approved' ? 'Đã duyệt đơn xin nghỉ việc' : 'Đã từ chối đơn xin nghỉ việc',
                'data' => $resignationRequest->load(['employee', 'reviewer'])
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi xử lý đơn',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết đơn xin nghỉ việc
     */
    public function show($id)
    {
        try {
            $resignationRequest = ResignationRequest::with(['employee', 'reviewer'])->find($id);
            
            if (!$resignationRequest) {
                return response()->json(['message' => 'Không tìm thấy đơn xin nghỉ việc'], 404);
            }

            return response()->json($resignationRequest, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể tải thông tin đơn xin nghỉ việc',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
