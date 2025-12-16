<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Helper for transforming between arrays, objects, and collections.
 *
 * Provides utilities to:
 * - Convert arrays of arrays to arrays of stdClass objects
 * - Convert arrays of objects (with `toArray`) to plain arrays
 * - Normalize arrays/collections to collections of objects
 * - Read nested values via dot-notation and validate required keys
 *
 * Intended usage:
 * - DTO hydration and normalization of payloads
 * - Repository/service layers working with mixed data structures
 * - Preparing data for serialization or export
 *
 * Guarantees:
 * - Input validation with informative exceptions for misuse
 * - Non-destructive transformations preserving existing keys/values
 *
 * See also: NumberHelper and DateTimeHelper for domain-specific utilities.
 */
class ArrayObjectHelper
{
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
}
