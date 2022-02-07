<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    // POST request is for storing a new user. PUT request is for updating an existing user.
    // Only the Super Admin can store new user and user's can only update their own account.
    public function authorize()
    {
        if ($this->method() == 'POST') {
            return Auth::user()->role === User::$SUPERADMIN;
        } else {
            return Auth::id() == $this->id;
        }
    }

    public function rules()
    {
        // Rules for adding new user (when request method is POST)
        if ($this->method() == 'POST') {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users',
                'mobile' => 'required|string|max:11|regex:/(01)[1-9]{9}/|unique:users',
                'password' => 'required|string|max:255|min:8',
                'passwordConfirm' => 'required|string|same:password'
            ];
        }
        // Rules for updating existing user (when request method is PUT or PATCH)
        else {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users,email,' . $this->id,
                'mobile' => 'required|string|max:11|regex:/(01)[0-9]{9}/|unique:users,mobile,' . $this->id
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => trans('validation.custom.name.required'),
            'email.required' => trans('validation.custom.email.required'),
            'email.unique' => trans('validation.custom.email.unique'),
            'mobile.required' => trans('validation.custom.mobile.required'),
            'mobile.max' => trans('validation.custom.mobile.max'),
            'mobile.regex' => trans('validation.custom.mobile.regex'),
            'mobile.unique' => trans('validation.custom.mobile.unique'),
            'password.required' => trans('validation.custom.password.required'),
            'password.min' => trans('validation.custom.password.min'),
            'passwordConfirm.required' => trans('validation.custom.passwordConfirm.required'),
            'passwordConfirm.same' => trans('validation.custom.passwordConfirm.same')
        ];
    }
}
