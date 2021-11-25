<?php

namespace App\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use function Sentry\captureException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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
        // render and log exception
        $this->renderable(function (Throwable $e) {

            $exceptionClass = get_class($e);

            Log::error('Exception handler, class: ' . $exceptionClass . ', error: ' . $e->getMessage());

            if ($e instanceof AuthenticationException) {
                // special case for authentication
                return response(['message' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
            } elseif ($e instanceof NotFoundHttpException) {
                $message = $e->getMessage() ? $e->getMessage() : '404 not found';
                // special case for 404
                return response(['message' => $message], Response::HTTP_NOT_FOUND);
            } else {
                // all other exceptions will return the same result
                return response(['status' => false, 'message' => $e->getMessage()], $e->getCode() ?: Response::HTTP_BAD_REQUEST);
            }

        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
