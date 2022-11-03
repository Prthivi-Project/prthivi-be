<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\ResponseFormatter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterUserController extends Controller
{
    use ResponseFormatter;
    //
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $token =  Auth::guard('api')->login($user);

        return $this->success(201, "Register has been successfully", [
            "user" => $user,
            "token" => [
                "access_token" => $token,
                "token_type" => "Bearer",
            ]
        ]);
    }
}
