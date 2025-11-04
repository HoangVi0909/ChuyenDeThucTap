<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\NotificationHelper;

class LeaveRequestController extends Controller
{
    // Nhân viên gửi đơn xin nghỉ phép
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|string',
            'reason' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Auth::user();
        if (!$employee) {
            return response()->json(['message' => 'Không xác định nhân viên'], 401);
        }

        $days = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        if ($request->type === 'annual' && $employee->annual_leave_days < $days) {
            return response()->json(['message' => 'Bạn không đủ ngày phép'], 400);
        }

        $leave = LeaveRequest::create([
            'employee_id' => $employee->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);
        // Gửi thông báo cho admin khi có đơn mới
        NotificationHelper::send(null, 'admin', 'Đơn xin nghỉ phép mới', 'Có đơn xin nghỉ phép mới từ nhân viên: ' . ($employee->name ?? ''));
        return response()->json(['success' => true, 'data' => $leave]);
    }

    // Admin duyệt/từ chối đơn
    public function review(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string',
        ]);
        $leave = LeaveRequest::findOrFail($id);
        if ($leave->status !== 'pending') {
            return response()->json(['message' => 'Đơn đã được xử lý'], 400);
        }
        DB::beginTransaction();
        try {
            $leave->status = $request->status;
            $leave->admin_note = $request->admin_note;
            $leave->reviewed_at = now();
            $leave->reviewed_by = Auth::guard('admin')->id();
            $leave->save();
            // Nếu duyệt và là phép năm thì trừ ngày phép
            if ($leave->status === 'approved' && $leave->type === 'annual') {
                $days = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
                $employee = $leave->employee;
                $employee->annual_leave_days = max(0, $employee->annual_leave_days - $days);
                $employee->save();
            }
            // Gửi thông báo cho nhân viên khi đơn được duyệt hoặc từ chối
            if ($leave->status === 'approved') {
                NotificationHelper::send($leave->employee_id, 'employee', 'Đơn nghỉ phép đã được duyệt', 'Đơn nghỉ phép của bạn đã được duyệt.');
            } elseif ($leave->status === 'rejected') {
                NotificationHelper::send($leave->employee_id, 'employee', 'Đơn nghỉ phép bị từ chối', 'Đơn nghỉ phép của bạn đã bị từ chối.');
            }
            DB::commit();
            return response()->json(['success' => true, 'data' => $leave]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi xử lý: ' . $e->getMessage()], 500);
        }
    }

    // Lấy danh sách đơn phép của nhân viên
    public function myRequests()
    {
        $employee = Auth::user();
        $requests = LeaveRequest::where('employee_id', $employee->id)->orderByDesc('created_at')->get();
        return response()->json($requests);
    }

    // Lấy tất cả đơn phép (admin)
    public function index(Request $request)
    {
        $query = LeaveRequest::with('employee');
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('employee_name') && $request->employee_name) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->employee_name . '%');
            });
        }
        $requests = $query->orderByDesc('created_at')->get();
        return response()->json($requests);
    }
}
