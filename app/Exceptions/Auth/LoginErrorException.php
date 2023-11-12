<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class LoginErrorException extends Exception
{
    private $responseCode;
    private $errorCode;
    private $errorMessage;

    public function __construct(
        $responseCode,
        $errorCode,
        $errorMessage
    ) {
        $this->responseCode = $responseCode;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
    }


    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::warning('User Auth error occured!');
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {

        $data = [
            'success'      => false,
            'errorCode'    => $this->errorCode,
            'message'      => $this->errorMessage,

        ];
        return new JsonResponse($data, $this->responseCode);
    }
}
