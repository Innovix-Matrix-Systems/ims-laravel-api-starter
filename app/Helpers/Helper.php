<?php

namespace App\Helpers;

use \Illuminate\Database\Eloquent\Model;

class Helper
{
    /**
     * Fill a model property with a given value if the value is not null.
     *
     * @param Model  $model    The model object to fill.
     * @param string $property The property to fill.
     * @param mixed  $value    The value to fill the property with.
     *
     * @return void
     */
    public static function fillModelIfNotNull(Model $model, string $property, mixed $value)
    {
        if ($value !== null) {
            $model->$property = $value;
        }
    }

    /**
     * Add leading zeros to a number, if necessary
     *
     * @param  int    $value     The number to add leading zeros
     * @param  int    $threshold Threshold for adding leading zeros (number of digits
     *                           that will prevent the adding of additional zeros)
     * @return string
     */
    public static function addLeadingZero(int $value, int $threshold = 2)
    {
        return sprintf('%0' . $threshold . 's', $value);
    }

    /**
     * Convert an array of arrays to an array of objects.
     *
     * @param  array $arrayOfArrays The array of arrays to convert
     * @return array The array of objects
     */
    public static function arraysToObjects(array $arrayOfArrays): array
    {
        return array_map(function ($item) {
            return (object) $item;
        }, $arrayOfArrays);
    }

    /**
    * Convert an array of objects to a plain array.
    *
     * @param  array $objects
     * @return array
    */
    public static function objectsToArray(array $objects): array
    {
        return array_map(fn ($object) => $object->toArray(), $objects);
    }
}
