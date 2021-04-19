<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\InvalidOrderException;

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
        $this->reportable(function (InvalidOrderException $e) {
            echo 99;
        });
    }


    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {
            return response()->json([
                'errors' => [  [
                    'status' => $e->getStatusCode(),
                    'title' =>  $e->getMessage()
                ]]
            ], $e->getStatusCode());
           // return $this->renderHttpException($e);
        } else {
            // Custom error 500 view on production
            if (app()->environment() == 'production') {
                return response()->view('errors.500', [], 500);
            }
            return parent::render($request, $e);
        }

    }

}
