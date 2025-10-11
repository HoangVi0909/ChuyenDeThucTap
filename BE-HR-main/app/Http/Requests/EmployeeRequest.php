<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

public function rules()
{
    $id = $this->route('employee');
    return [
        'name' => 'required|string|max:255',
        'gender' => 'nullable|in:Nam,Nữ,Khác',
        'email' => 'nullable|email|max:255|unique:employees,email,' . $id,
        'cccd' => 'required|unique:employees,cccd,' . $id,
        'username' => 'required|unique:employees,username,' . $id,
        'password' => $id ? 'sometimes|string|min:6' : 'required|string|min:6',
        'position_id' => 'required|exists:positions,id',
        'department_id' => 'required|exists:departments,id',
        'birth_date' => 'nullable|date',
        'qualification' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'photo' => 'nullable|string|max:500',
    ];
}
}