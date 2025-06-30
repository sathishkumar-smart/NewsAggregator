<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use App\Base\Exceptions\CustomValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class Handler extends ExceptionHandler
{
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $exception->errors(),
        ], 422);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($this->expectsJson($request)) {
            return $this->getJsonResponse($exception);
        }

        return parent::render($request, $exception);
    }

    protected function getJsonResponse(Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        $statusCode = $this->getStatusCode($exception);

        if ($exception instanceof NotFoundHttpException || !($message = $exception->getMessage())) {
            $message = sprintf('%d %s', $statusCode, Response::$statusTexts[$statusCode]);
        }

        if ($exception instanceof QueryException) {
            $message = 'Internal Server Error';
        }

        $data = [
            'success' => false,
            'message' => $message,
            'status_code' => $statusCode,
        ];

        if ($exception instanceof ValidationException || $exception instanceof CustomValidationException) {
            $data['status_code'] = $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data['errors'] = $exception instanceof ValidationException ?
                $exception->validator->errors()->getMessages() : $exception->getMessages();
        }

        if ($code = $exception->getCode()) {
            $data['code'] = $code;
        }

        if (env('APP_DEBUG')) {
            $data['debug'] = [
                'line' => $exception->getLine(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'trace' => explode('\n', $exception->getTraceAsString()),
            ];
        }

        return response()->json($data, $statusCode);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->expectsJson($request)) {
            return response()->json(['error' => 'Unauthenticated.'], Response::HTTP_UNAUTHORIZED);
        }

        return redirect()->guest('/login');
    }

    /**
     * Get the exception status code
     *
     * @param Throwable $exception
     * @param int $defaultStatusCode
     * @return int
     */
    protected function getStatusCode(Throwable $exception, $defaultStatusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        if ($this->isHttpException($exception)) {
            return $exception->getStatusCode();
        }

        return $exception instanceof AuthenticationException ?
            Response::HTTP_UNAUTHORIZED :
            $defaultStatusCode;
    }


    /**
     * Check if the current request expects a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function expectsJson($request)
    {
        if ($request->expectsJson()) {
            return true;
        }

        return (!empty($request->segments()) && $request->segments()[0] === 'api');
    }
}
