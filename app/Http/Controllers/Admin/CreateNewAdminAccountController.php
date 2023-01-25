<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateNewAdminAccountRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CreateNewAdminAccountController extends Controller
{
    public function register(CreateNewAdminAccountRequest $request)
    {

        $payload = $request->safe()->except("password", 'avatar_file', 'avatar_base64');
        $payload['role_id'] = Role::$admin; // admin

        $payload['avatar'] = \fake()->imageUrl();
        $payload['password'] = Hash::make($request->password);


        $user = User::create($payload);

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
