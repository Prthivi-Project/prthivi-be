<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\Auth\Api\EmailVerificationRequest;
use App\Traits\ResponseFormatter;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController
{
    use ResponseFormatter;
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return \abort(409, "This email has verified email");
        }

        if ($request->user()->markEmailAsVerified()) {
            \event(new Verified($request->user("api")));
        }

        return $this->success(200, "Email verification successfull", null);
    }
}
