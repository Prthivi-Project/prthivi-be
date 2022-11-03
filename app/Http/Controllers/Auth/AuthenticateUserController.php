<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Api\LoginRequest;
use App\Traits\ResponseFormatter;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticateUserController extends Controller
{
    use ResponseFormatter;
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register']]);
    }
    //
    public function login(LoginRequest $request)
    {
        $token = $this->authenticate($request);

        $user = Auth::user();
        return $this->success(200, "Login Successs", [
            "user" => $user,
            "token" => [
                "access_token" => $token,
                "token type" => "Bearer"
            ]
        ]);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return $this->success(200, "Logout success", null);
    }

    public function refresh()
    {
        return $this->success(200, "Refresh has been successfully", [
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]

        ]);
    }

    public function me()
    {
        return $this->success(200, "OK", Auth::guard('api')->user());
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(Request $request)
    {
        $this->ensureIsNotRateLimited($request);
        $token = Auth::guard("api")->attempt($request->only('email', 'password'), $request->boolean('remember'));
        if (!$token) {
            RateLimiter::hit($this->throttleKey($request->email, $request->ip()));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request->email, $request->ip()));

        return $token;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request->input("email"), $request->ip()), 5)) {
            return;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request->email, $request->ip()));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }


    public function throttleKey($email, $ip)
    {
        return Str::transliterate(Str::lower($email) . '|' . $ip);
    }
}
