<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Employee;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('employee')->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.notifications.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'notification_type' => 'required|in:info,warning,success,error',
            'is_read' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        Notification::create($request->all());

        return redirect()->route('admin.notifications.index')
                        ->with('success', 'Thông báo đã được tạo thành công!');
    }

    public function show(Notification $notification)
    {
        $notification->load('employee');
        return view('admin.notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.notifications.edit', compact('notification', 'employees'));
    }

    public function update(Request $request, Notification $notification)
    {
        $request->validate([
            'employee_id' => 'nullable|exists:employees,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'notification_type' => 'required|in:info,warning,success,error',
            'is_read' => 'boolean',
            'status' => 'required|in:active,inactive'
        ]);

        $notification->update($request->all());

        return redirect()->route('admin.notifications.index')
                        ->with('success', 'Thông báo đã được cập nhật thành công!');
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()->route('admin.notifications.index')
                        ->with('success', 'Thông báo đã được xóa thành công!');
    }
}