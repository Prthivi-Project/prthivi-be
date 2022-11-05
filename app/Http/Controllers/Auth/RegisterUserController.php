<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\MediaUpload;
use App\Traits\ResponseFormatter;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Faker\Generator;

class RegisterUserController extends Controller
{
    use ResponseFormatter;
    use MediaUpload;


    private $dirName = 'avatar';
    //
    public function register(RegisterRequest $request)
    {
        $payload = $request->safe()->except("password", 'avatar_file', 'avatar_base64');
        $payload['role_id'] = 4; // customer
        $avatar_file = $request->file("avatar_file");
        $avatar_base64 = $request->input("avatar_base64");

        if ($avatar_base64) {
            $this->checkAndCreateDirIfNotExist(self::$dirName);
            $filePath = $this->storeMediaAsBased64($avatar_base64, self::$dirName);
            if (!$filePath) {
                return $this->error(500, "Error while uploading avatar", null);
            }
            $payload['avatar'] = \asset('storage' . $filePath);
        } elseif ($avatar_file) {
            $this->checkAndCreateDirIfNotExist(self::$dirName);
            $filePath = $this->storeMediaAsFile($avatar_file, self::$dirName);
            if (!$filePath) {
                return $this->error(500, "Error while uploading avatar", null);
            }
            $payload['avatar'] = \asset('storage' . $filePath);
        } else {
            $payload['avatar'] = \fake()->imageUrl();
        }
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
