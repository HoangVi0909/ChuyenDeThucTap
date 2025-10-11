<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->route('admin');
        return [
            'username' => 'required|unique:admins,username,' . $id,
            'password' => $id ? 'sometimes' : 'required',
            // ...existing code...
        ];
    }
}
