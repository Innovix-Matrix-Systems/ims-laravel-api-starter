<?php

namespace App\Helpers;

/**
 * Helper for decimal formatting and precision-aware float comparisons.
 *
 * Provides utilities to:
 * - Round numbers to a fixed number of decimal places
 * - Compare floats using a small tolerance to mitigate precision issues
 * - Check greater-or-equal relationships with tolerance compensation
 *
 * Intended usage:
 * - Monetary calculations and display formatting
 * - Validations where floating-point equality must be robust
 * - Business rules comparing measured values with thresholds
 *
 * Guarantees:
 * - Deterministic rounding via PHP `round`
 * - Consistent comparisons using a class-defined tolerance
 *
 * See also: NumberToWordsHelper for textual number representation.
 */
class NumberHelper
{
    private const DECIMAL_PLACES = 2;
    private const DECIMAL_TOLERANCE = 0.01;

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

    /**
     * Checks if first float is greater than or equal to second float with precision
     *
     * @param float $a first float
     * @param float $b second float
     */
    public static function isFloatGreaterOrEqual(float $a, float $b): bool
    {
        return $a >= ($b - self::DECIMAL_TOLERANCE);
    }
}
