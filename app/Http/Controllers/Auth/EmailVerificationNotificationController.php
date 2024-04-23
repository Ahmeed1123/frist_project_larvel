<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function create()
    {
        return view('auth.verify-email.notice');
    }

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'status' => 'success',
            'message' => 'لقد تم إرسال رابط التحقق من البريد الإلكتروني مرةً أخرى إلى عنوان البريد الإلكتروني الذي قدمته أثناء التسجيل.'
        ]);
    }
}
