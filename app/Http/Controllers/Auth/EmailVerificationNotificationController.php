<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class EmailVerificationNotificationController extends Controller
{

    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            \abort(403, "You have been verified your email.");
        }
        $request->cookie("token", JWTAuth::getToken());
        $request->user()->sendEmailVerificationNotification();

        return $this->success(200, 'Verification email link sent to your email', null);
    }
}
