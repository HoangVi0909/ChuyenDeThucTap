<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Employee;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('employee')->get();
        return view('admin.feedback.index', compact('feedbacks'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.feedback.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'feedback_type' => 'required|in:performance,complaint,suggestion,other',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:pending,reviewed,resolved'
        ]);

        Feedback::create($request->all());

        return redirect()->route('admin.feedback.index')
                        ->with('success', 'Phản hồi đã được tạo thành công!');
    }

    public function show(Feedback $feedback)
    {
        $feedback->load('employee');
        return view('admin.feedback.show', compact('feedback'));
    }

    public function edit(Feedback $feedback)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('admin.feedback.edit', compact('feedback', 'employees'));
    }

    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'feedback_type' => 'required|in:performance,complaint,suggestion,other',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'status' => 'required|in:pending,reviewed,resolved'
        ]);

        $feedback->update($request->all());

        return redirect()->route('admin.feedback.index')
                        ->with('success', 'Phản hồi đã được cập nhật thành công!');
    }

    public function destroy(Feedback $feedback)
    {
        $feedback->delete();

        return redirect()->route('admin.feedback.index')
                        ->with('success', 'Phản hồi đã được xóa thành công!');
    }
}