<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:255',
            'confirm_new_password' => 'required|string|same:new_password'
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => trans('validation.custom.current_password.required'),
            'new_password.required' => trans('validation.custom.new_password.required'),
            'new_password.min' => trans('validation.custom.new_password.min'),
            'confirm_new_password.required' => trans('validation.custom.confirm_new_password.required'),
            'confirm_new_password.same' => trans('validation.custom.confirm_new_password.same'),
        ];
    }
}
