<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

abstract class Controller
{
    /**
     * Build a standardized JSON response for API data.
     *
     * - For `JsonResource` or `AnonymousResourceCollection`, uses the resource's
     *   serialized payload and merges optional `meta`.
     * - For `Collection`, `Arrayable`, and arrays, wraps the payload under `data`
     *   and appends optional `meta`.
     *
     * @param \Illuminate\Http\Resources\Json\JsonResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Support\Collection|\Illuminate\Contracts\Support\Arrayable|array|mixed $result
     * @param int                                                                                                                                                                                         $code   HTTP status code
     * @param array                                                                                                                                                                                       $meta   Additional metadata to merge into the response
     */
    protected function respond($result = null, int $code = Response::HTTP_OK, array $meta = []): JsonResponse
    {
        if ($result instanceof JsonResource || $result instanceof AnonymousResourceCollection) {
            $payload = $result->response()->getData(true);
            if (! empty($meta)) {
                $payload['meta'] = array_merge($payload['meta'] ?? [], $meta);
            }

            return new JsonResponse($payload, $code);
        }

        if ($result instanceof Collection) {
            $data = $result->toArray();
        } elseif ($result instanceof Arrayable) {
            $data = $result->toArray();
        } elseif (is_array($result)) {
            $data = $result;
        } else {
            $data = $result;
        }

        $body = ['data' => $data];
        if (! empty($meta)) {
            $body['meta'] = $meta;
        }

        return new JsonResponse($body, $code);
    }

    /**
     * Throw an RFC 9457-compliant API error.
     *
     * Builds and throws `ApiException`, which renders a standardized Problem
     * Details response via the exception handler. This method does not return.
     *
     * @param string $errorCode  Application-specific error code (see `ApiErrorCode`)
     * @param string $message    Human-readable error detail
     * @param int    $code       HTTP status code
     * @param array  $additional Extra context to merge into the error response
     *
     * @throws \App\Exceptions\ApiException
     */
    protected function fail(string $errorCode, string $message, int $code = Response::HTTP_BAD_REQUEST, array $additional = []): JsonResponse
    {
        throw new ApiException(
            responseCode: $code,
            errorCode: $errorCode,
            errorMessage: $message,
            additionalData: $additional
        );
    }

    /**
     * Return a 204 No Content response.
     *
     * Used when an operation is successful but there is no data to return
     * (e.g., after deleting a resource).
     */
    protected function successNoContent(): JsonResponse
    {
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
