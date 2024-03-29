<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * return  response.
     *
     * @param array | Collection | AnonymousResourceCollection | JsonResource $result
     * @param string                                                          $message
     * @param int                                                             $code
     *
     * @return JsonResponse
     */
    public function sendSuccessResponse($result = [], $message = '', $code = Response::HTTP_OK)
    {
        $response = [
            'data'      => $result,
            'success'   => true,
            'message'   => $message,
        ];
        if (empty($result)) {
            unset($response['data']);
        }
        return new JsonResponse($response, $code);
    }

    /**
     * return  response with collection.
     *
     * @param Collection | AnonymousResourceCollection $collection
     * @param string                                   $message
     * @param int                                      $code
     *
     * @return JsonResponse
     */
    public function sendSuccessCollectionResponse($collection, $message, $code = Response::HTTP_OK)
    {
        $response = [
            'data'      => $collection,
            'success'   => true,
            'message'   => $message,
        ];

        return new JsonResponse($response, $code);
    }

    /**
     * return error response.
     *
     * @param       $error
     * @param array $errorMessages
     * @param int   $code
     *
     * @return JsonResponse
     */
    public function sendErrorResponse($error, $errorMessages = [], $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }
        if (empty($error)) {
            unset($response['message']);
        }

        return new JsonResponse($response, $code);
    }
}
