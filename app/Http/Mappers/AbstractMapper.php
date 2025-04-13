<?php

namespace App\Http\Mappers;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 * @template TDTO of object
 */
abstract class AbstractMapper
{
    /**
     * Map DTO to Model with optional extra parameters
     *
     * @param  TDTO   $dto
     * @param  TModel $model
     * @return TModel
     */
    abstract public static function toModel($dto, Model $model, array $extra = []): Model;

    /**
     * Fill model with base audit fields
     *
     * @param TModel $model
     */
    protected static function fillAuditFields(Model $model, bool $isCreate = true): void
    {
        if ($isCreate) {
            $model->created_by = auth()->id();
        } else {
            $model->updated_by = auth()->id();
        }
    }
}
