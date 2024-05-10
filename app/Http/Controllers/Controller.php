<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
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
     * @param Collection | AnonymousResourceCollection | JsonResource $collection
     * @param string                                                  $message
     * @param int                                                     $code
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

    /**
     * Apply search filters to a model.
     *
     * @param string|null $searchText   The text to search for.
     * @param Builder     $model        The model to search on.
     * @param string[]    $searchFields The fields to search in.
     *
     * @return Builder The filtered model.
     */
    public function applySearchFilters(
        ?string $searchText,
        Builder $model,
        array $searchFields
    ): Builder {
        if (empty($searchText)) {
            return $model;
        }

        return $model->where(function (Builder $query) use ($searchText, $searchFields) {
            $query->where(function (Builder $query) use ($searchText, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', "%{$searchText}%");
                }
            });
        });
    }

    /**
     * Apply Relation Search Filters to a model.
     *
     * @param  string   $relation     The relation to search within.
     * @param  string   $searchText   The text to search for within the relation.
     * @param  string[] $searchFields The fields to search in within the relation.
     * @param  Builder  $model        The model to search on.
     * @return Builder  The filtered model.
     */
    public function applyRelationSearchFilters(
        string $relation,
        string $searchText,
        array $searchFields,
        Builder $model
    ): Builder {
        if (empty($searchText)) {
            return $model;
        }
        return $model->whereHas($relation, function ($query) use ($searchText, $searchFields) {
            $query->where(function ($query) use ($searchText, $searchFields) {
                foreach ($searchFields as $field) {
                    $query->orWhere($field, 'LIKE', "%{$searchText}%");
                }
            });
        });
    }

    /**
     * Apply Select filters to a model.
     *
     * @param Builder              $model        The model to search on.
     * @param array<string, mixed> $selectFields The fields to search in.
     *
     * @return Builder The filtered model.
     */
    public function applySelectFilters(
        Builder $model,
        array $selectFields
    ): Builder {
        $selectFields = array_filter($selectFields, function ($value) {
            return !is_null($value) && $value !== '';
        });
        return $model->where(function (Builder $query) use ($selectFields) {
            foreach ($selectFields as $field => $value) {
                $query->where($field, '=', $value);
            }
        });
    }

    /**
     * Apply Filters to a model.
     *
     * @param  ?string              $searchText   The text to search for.
     * @param  array<string>        $searchFields The fields to search in.
     * @param  array<string, mixed> $selectFields The fields to select.
     * @param  Builder              $model        The model to search on.
     * @param  ?string              $relation     The model relationship to search on.
     * @return Builder              The filtered model.
     */
    public function applyFilters(
        ?string $searchText,
        array $searchFields,
        array $selectFields,
        Builder $model,
        ?string $relation = null,
    ): Builder {

        if ($searchText && !empty($searchFields)) {
            if($relation) {
                $model = $this->applyRelationSearchFilters($relation, $searchText, $searchFields, $model);
            } else {
                $model = $this->applySearchFilters($searchText, $model, $searchFields);
            }
        }

        if (!empty($selectFields)) {
            return $this->applySelectFilters($model, $selectFields);
        }

        return $model;
    }
}
