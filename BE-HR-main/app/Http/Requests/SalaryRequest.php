<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|integer|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'month' => 'required|date_format:Y-m',
            'note' => 'nullable|string|max:1000'
        ];
    }

    public function messages(): array
    {
        return [
            'employee_id.required' => 'Vui lòng chọn nhân viên',
            'employee_id.exists' => 'Nhân viên không tồn tại',
            'amount.required' => 'Vui lòng nhập số tiền lương',
            'amount.numeric' => 'Số tiền lương phải là số',
            'amount.min' => 'Số tiền lương phải lớn hơn 0',
            'month.required' => 'Vui lòng chọn tháng',
            'month.date_format' => 'Định dạng tháng không đúng (Y-m)',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự'
        ];
    }
}
