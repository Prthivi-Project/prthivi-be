<?php

namespace App\Exceptions;

use App\Traits\ResponseFormatter;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use PDO;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseFormatter;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Throwable $th, Request $request) {
            if ($request->is("api/*")) {
                if ($th instanceof ValidationException) {
                    return $this->error(422, $th->getMessage(), $th->errors());
                } elseif ($th instanceof NotFoundHttpException) {
                    return $this->error(Response::HTTP_NOT_FOUND, "NOT FOUND", "Resource not found in our record");
                } elseif ($th instanceof UnauthorizedHttpException) {
                    return $this->error(Response::HTTP_UNAUTHORIZED, "Unauthorized", $th->getMessage());
                } elseif ($th instanceof BadRequestHttpException) {
                    return $this->error(Response::HTTP_BAD_REQUEST, "Bad Request", $th->getMessage());
                } elseif ($th instanceof AccessDeniedHttpException) {
                    return $this->error(Response::HTTP_FORBIDDEN, "Forbidden", $th->getMessage());
                }
            }
        });
    }
}
