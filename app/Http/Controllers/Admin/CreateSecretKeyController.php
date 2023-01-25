<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecretKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateSecretKeyController extends Controller
{
    public function create(Request $request)
    {
        $token =  SecretKey::create([
            "token" => \random_int(10000, 999999)
        ]);

        return $this->success(200, "Token created successfully", [
            "token" => $token
        ]);
    }
}
