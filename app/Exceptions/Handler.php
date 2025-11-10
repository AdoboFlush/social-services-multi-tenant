<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Http\Exceptions\PostTooLargeException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            return back()->with('error',_lang('Your session has expired. Please try again.'));
        }

        if ($exception instanceof PostTooLargeException) {
            return back()->with('error',_lang('Post too large. Please try again'));
        }

        if ($request->expectsJson()) {
            if ($exception instanceof UnauthorizedHttpException) {
                return response()->json(
                    [   
                        'success' => false,
                        'error_code' => 'E-UNAUTHENTICATED',
                        'data' => 'Token Unauthenticated'
                    ], 401
                );
            }
        }

        return parent::render($request, $exception);
    }
}
