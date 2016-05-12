<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $response = [
            'message' => (string) $e->getMessage(),
        ];

        if ($e instanceof ValidationException) {
            $messages = $e->validator->messages()->toArray();
            $errors = [];
            foreach ($messages as $key => $value) {
                $error = [
                    'code' => $key,
                    'message' => $value
                ];
                $errors[] = $error;
            }
            $response['errors'] = $errors;

            return response()->json($response, 400);
        }

        if ($e instanceof ModelNotFoundException) {
            $response['message'] = 'Not Found';
            return response()->json($response, 404);
        }

        if ($e instanceof HttpException) {
            $response['message'] = Response::$statusTexts[$e->getStatusCode()];
            return response()->json($response, $e->getStatusCode());
        }

        return $response()->json($response, 500);
    }
}
