<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $status = Password::sendResetLink($request->only('email'));
        $sent = $status === Password::RESET_LINK_SENT;
        return jsonResponse(message: $sent ? 'OK' : 'Error', status: $sent ? 200 : 500);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        $error = match ($status) {
            Password::INVALID_USER => ['email' => 'Invalid User'],
            Password::INVALID_TOKEN => ['token' => 'Invalid Token'],
            default => []
        };

        if (!empty($error)) {
            return jsonResponse(
                message: 'Error in reset password',
                errors: $error,
                status: 500
            );
        }

        return jsonResponse();
    }
}
