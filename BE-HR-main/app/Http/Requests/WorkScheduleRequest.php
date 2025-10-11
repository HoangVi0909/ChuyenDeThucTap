<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'work_date' => 'required|date',
            'shift' => 'required|in:S,C',
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Vui lòng chọn nhân viên',
            'employee_id.exists' => 'Nhân viên không tồn tại',
            'work_date.required' => 'Vui lòng chọn ngày làm việc',
            'work_date.date' => 'Ngày làm việc không hợp lệ',
            'shift.required' => 'Vui lòng chọn ca làm việc',
            'shift.in' => 'Ca làm việc không hợp lệ (S: Ca sáng, C: Ca chiều)',
        ];
    }
}
