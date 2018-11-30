<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\ Auth\AuthenticationException;

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
        if($exception instanceof ValidationException){

            if(request()->ajax() || request()->wantsJson()){

                foreach($exception->errors() as $key => $value){

                    $error  = $value[0];
                    break;
                }

                return response()->json([

                    'status'    => false, 
                    'data'      => $error

                ], 200);

            }
            
        }

        if($exception instanceof AuthenticationException){

            if(request()->ajax() || request()->wantsJson()){

                return response()->json([

                    'status'    => false, 
                    'data'      => 'Unauthorized.'

                ], 401);

            }
            
        }
        
        return parent::render($request, $exception);
    }
}
