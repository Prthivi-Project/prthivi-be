<?php

namespace App\Traits;

trait ResponseFormatter
{
    public function success($code, $message, $data)
    {
        return response()->json([
            "meta" => [
                "status" => "success",
                "code" => $code,
                "message" => $message
            ],
            "data" => $data
        ], $code);
    }

    public function error($code, $message, $error)
    {
        return response()->json([
            "meta" => [
                "status" => "error",
                "code" => $code,
                "message" => $message
            ],
            "error" => $error
        ], $code);
        //
    }
}
