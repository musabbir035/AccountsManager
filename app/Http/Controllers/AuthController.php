<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $field = $this->checkLoginField($request);
        $username = $request->input('username');
        $password = $request->input('password');
        $remember = $request->input('remember');

        $msg = trans('messages.failed_auth_email');
        if ($field === 'mobile') {
            $msg = trans('messages.failed_auth_mobile');
        }

        if (!Auth::attempt([$field => $username, 'password' => $password], $remember)) {
            return response([
                'message' => $msg
            ], 422);
        }

        $request->session()->regenerate();

        return response([
            'user' => Auth::user(),
            'message' => trans('messages.success')
        ], 200);
    }

    public function checkLoginField($request)
    {
        $field = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        return $field;
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response(['message' => trans('messages.success')], 200);
    }

    public function authCheck()
    {
        if (Auth::user()) {
            return response([
                'message' => trans('messages.user_authenticated')
            ], 200);
        }
        return response([
            'message' => trans('messages.user_not_authenticated')
        ], 401);
    }
}
