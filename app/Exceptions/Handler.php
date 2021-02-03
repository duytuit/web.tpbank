<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        // if ($exception instanceof ValidationException)
        if ($exception instanceof AuthenticationException) 
        {
            return response()->json([ 'success' => false, 'message' => 'Unauthenticated.'], 401);
        }
        // detect instance
        if ($exception instanceof UnauthorizedHttpException) {
            // detect previous instance
            if ($exception->getPrevious() instanceof TokenExpiredException) {
                return response()->json(['success' => false, 'message' => 'TOKEN_EXPIRED'], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenInvalidException) {
                return response()->json(['success' => false, 'message' => 'TOKEN_INVALID'], $exception->getStatusCode());
            } else if ($exception->getPrevious() instanceof TokenBlacklistedException) {
                return response()->json(['success' => false,'message' => 'TOKEN_BLACKLISTED'], $exception->getStatusCode());
            } else {
                return response()->json(['success' => false, 'message' => "UNAUTHORIZED_REQUEST"], 401);
            }
        }
        $items = $exception->validator->getMessageBag()->toArray();
        $data_array=[];
        foreach ($items as $key => $value) {
            $data_array[]=$value[0];
        }
        $data = [
            'success' => false,
            //'message'  => $exception->validator->getMessageBag()->toArray()['message'][0] ?? trans('general.validation') ?? null,
            'message'  => $data_array ?? trans('general.validation') ?? null,
            'errors' => $exception->validator->getMessageBag() ?? null,
        ];
        //$data[]=$exception->validator->getMessageBag();
        return response()->json($data, 422);

        // return response()->json(['errors' => $exception->validator->getMessageBag()], 422);

        // if it's API, hack to return JSON
        $isApiCall = (strpos($request->getUri(), '/api') !== false);
        if ($isApiCall) {
            $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        }
        return parent::render($request, $exception);
    }
}
