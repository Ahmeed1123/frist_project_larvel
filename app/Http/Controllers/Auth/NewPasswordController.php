<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        $token = $request->route()->parameter('token');
        $email = $request->email;

        return view('auth.passwords.reset', compact('token', 'email'));
    }


    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return redirect()->route('login')->with([
            'alert' => [
                'type' => 'success',
                'title' => __('تمت العملية بنجاح!'),
                'body' => __('تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة.'),
            ]
        ]);
    }

    /**
     * Handle update password request from profile page.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults(), 'different:current_password'],
        ]);

        // check if the current password is correct
        if (!Hash::check($request->current_password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('كلمة المرور الحالية غير صحيحة.')],
            ]);
        }

        $user = $request->user();

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->route('user.profile.edit', $user->id)->with([
            'alert' => [
                'type' => 'success',
                'title' => __('تمت العملية بنجاح!'),
                'body' => __('تم تغيير كلمة المرور بنجاح.'),
            ]
        ]);
    }
}
