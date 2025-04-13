<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Helper
{
    private const DECIMAL_PLACES = 2;
    private const DECIMAL_TOLERANCE = 0.01;

    /**
     * Fill a model property with a given value if the value is not null.
     *
     * @param Model  $model    The model object to fill.
     * @param string $property The property to fill.
     * @param mixed  $value    The value to fill the property with.
     *
     * @throws InvalidArgumentException If property doesn't exist on model
     */
    public static function fillModelIfNotNull(Model $model, string $property, mixed $value): void
    {
        if ($value !== null) {
            if (! property_exists($model, $property) && ! method_exists($model, $property)) {
                throw new InvalidArgumentException("Property {$property} does not exist on model " . get_class($model));
            }
            $model->$property = $value;
        }
    }

    /**
     * Add leading zeros to a number.
     *
     * @param int $value     The number to add leading zeros
     * @param int $threshold Number of total digits desired
     *
     * @throws InvalidArgumentException If threshold is less than 1
     */
    public static function addLeadingZero(int $value, int $threshold = 2): string
    {
        if ($threshold < 1) {
            throw new InvalidArgumentException('Threshold must be greater than 0');
        }

        return str_pad((string) $value, $threshold, '0', STR_PAD_LEFT);
    }

    /**
     * Convert an array of arrays to an array of objects.
     *
     * @param array $arrayOfArrays The array of arrays to convert
     *
     * @throws InvalidArgumentException If input is not array of arrays
     *
     * @return array The array of objects
     */
    public static function arraysToObjects(array $arrayOfArrays): array
    {
        foreach ($arrayOfArrays as $item) {
            if (! is_array($item)) {
                throw new InvalidArgumentException('Input must be an array of arrays');
            }
        }

        return array_map(fn ($item) => (object) $item, $arrayOfArrays);
    }

    /**
     * Convert an array of objects to a plain array.
     *
     * @param array $objects Array of objects with toArray method
     *
     * @throws InvalidArgumentException If any object doesn't have toArray method
     */
    public static function objectsToArray(array $objects): array
    {
        foreach ($objects as $object) {
            if (! method_exists($object, 'toArray')) {
                throw new InvalidArgumentException('All objects must implement toArray method');
            }
        }

        return array_map(fn ($object) => $object->toArray(), $objects);
    }

    /** Convert any array or collection to collection of objects. */
    public static function toObjectCollection(array|Collection $items): Collection
    {
        $items = is_array($items) ? collect($items) : $items;

        return $items->map(fn ($item) => is_array($item) ? (object) $item : $item);
    }

    /** Safely get a value from an array or object using dot notation. */
    public static function getValue(array|object $data, string $key, mixed $default = null): mixed
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        foreach (explode('.', $key) as $segment) {
            if (! is_array($data) || ! array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }

    /** Check if all required keys exist in an array or object. */
    public static function hasRequiredKeys(array|object $data, array $required): bool
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        foreach ($required as $key) {
            if (! array_key_exists($key, $data) || $data[$key] === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Formats a float number to 2 decimal places
     *
     * @param float $number       the floating number
     * @param int   $decimalPlace the decimal places after floating number
     */
    public static function formatDecimal(float $number, int $decimalPlace = self::DECIMAL_PLACES): float
    {
        return round($number, $decimalPlace);
    }

    /**
     * Compares two float values with precision
     *
     * @param float $a first float
     * @param float $b second float
     */
    public static function isFloatEqual(float $a, float $b): bool
    {
        return abs($a - $b) < self::DECIMAL_TOLERANCE; // Tolerance of 0.01 for 2 decimal places
    }
}
