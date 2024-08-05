<?php

namespace App\Exceptions;

use App\Traits\ResponseTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        if ($exception instanceof ModelNotFoundException ||$exception instanceof NotFoundHttpException) {
            return $this->returnError('Not Found');
        }

        // if ($exception instanceof AccessDeniedHttpException) {
        //     return response()->json(['result'=>'false','message'=>'This action is unauthorized'],Response::HTTP_FORBIDDEN);
        // }
        if ($exception instanceof AccessDeniedHttpException) {
                dd($exception->getMessage());
                return $this->returnError('This action is unauthorized','403');
        }
        if($exception instanceof MethodNotAllowedHttpException ){
            return response()->json(['result'=>'false','message'=>'Method Not Allowed'],Response::HTTP_METHOD_NOT_ALLOWED);

        }
        // Unauthenticated
        if ($exception instanceof AuthenticationException) {
            return response()->json(['result'=>'false','message'=>'You Are Unauthenticated'],Response::HTTP_UNAUTHORIZED);

        }
        
        return parent::render($request, $exception);
    }
}
