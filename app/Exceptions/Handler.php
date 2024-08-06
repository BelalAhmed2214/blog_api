<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ResponseTrait;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            return $this->returnError('Link Not Found');
        }
        if ($exception instanceof ModelNotFoundException) {
            return $this->returnError('Model Not Found');

        }
        //unauthorized
        if ($exception instanceof AuthorizationException) {
            return $this->returnError('You are unauthorized', Response::HTTP_FORBIDDEN);
        }
        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->returnError('Method Not Allowed', Response::HTTP_METHOD_NOT_ALLOWED);

        }
        // Unauthenticated
        if ($exception instanceof AuthenticationException) {
            return $this->returnError('You are unauthenticated', Response::HTTP_UNAUTHORIZED);

        }

        return parent::render($request, $exception);
    }
}