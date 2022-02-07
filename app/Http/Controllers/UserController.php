<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response([
            'users' => UserResource::collection(User::withTrashed()->get())
        ], 200);
    }

    public function store(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->input('password')),
            'role' => 2
        ]);

        return response([
            'message' => trans('messages.user_added'),
            'user' => new UserResource($user)
        ], 200);
    }

    public function user()
    {
        return response([
            'user' => new UserResource(Auth::user())
        ], 200);
    }

    // soft delete or restore soft deleted user
    public function updateStatus($id)
    {
        if (Auth::user()->role === 1 && Auth::id() != $id) {
            $user = User::withTrashed()->findOrFail($id);
            if ($user->trashed()) {
                $user->restore();
                $msg = trans('messages.user_activated');
            } else {
                $user->delete();
                $msg = trans('messages.user_deactivated');
            }
            return response([
                'message' => $msg,
                'user' => new UserResource($user)
            ], 200);
        }

        return response([
            'message' => trans('messages.no_permission')
        ], 403);
    }

    // force/permanently delete
    public function destroy($id)
    {
        if (Auth::user()->role === 1 && Auth::id() != $id) {
            $user = User::findOrFail($id);
            $user->forceDelete();
            return response([
                'message' => trans('messages.user_deleted')
            ], 200);
        }

        return response([
            'message' => trans('messages.no_permission')
        ], 403);
    }

    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->safe()->only(['name', 'email', 'mobile']));

        return response([
            'message' => 'User updated',
            'user' => new UserResource($user)
        ], 200);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (Hash::check($request->input('current_password'), $user->password)) {
            $user->password = Hash::make($request->input('new_password'));
            $user->save();
            return response([
                'message' => trans('messages.password_changed'),
            ], 200);
        }

        return response([
            'message' => 'Invalid data',
            'errors' => [
                'current_password' => trans('validation.custom.current_password.wrong')
            ]
        ], 200);
    }
}
