<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'username' => ['required', 'string', function ($attribute, $value, $fail) {
                // Find user by email or mobile
                if (!User::where('email', $value)->orWhere('mobile', $value)->first()) {
                    $fail(trans('validation.custom.username.not_found'));
                }
            }],
            'password' => 'required|string',
            'remember' => 'nullable|boolean'
        ];
    }

    public function messages()
    {
        return [
            'username.required' => trans('validation.custom.username.required'),
            'password.required' => trans('validation.custom.password.required'),
        ];
    }
}
