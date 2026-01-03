<?php

namespace App\Helpers;

/**
 * Helper for converting numbers to human-readable words and fractions.
 *
 * Provides utilities to:
 * - Convert integers/decimals to English words with currency/fraction units
 * - Render print-friendly fractions using Unicode symbols where available
 * - Format large numbers into grouped words (thousands → trillions)
 * - Handle negative values and precision-tolerant fraction matching
 *
 * Intended usage:
 * - Invoices, receipts, and payment printouts
 * - Report generation and PDF exports requiring textual numbers
 * - UI display where fractions are preferred over decimals
 *
 * Guarantees:
 * - No external network calls; pure PHP transformations
 * - Stable formatting for common fractions with configurable tolerance
 * - Graceful handling of zero and negative numbers
 *
 * See also: NumberHelper for decimal rounding and float comparisons.
 */
class NumberToWordsHelper
{
    private static $units = [
        '',
        'One',
        'Two',
        'Three',
        'Four',
        'Five',
        'Six',
        'Seven',
        'Eight',
        'Nine',
        'Ten',
        'Eleven',
        'Twelve',
        'Thirteen',
        'Fourteen',
        'Fifteen',
        'Sixteen',
        'Seventeen',
        'Eighteen',
        'Nineteen',
    ];

    private static $tens = [
        '',
        '',
        'Twenty',
        'Thirty',
        'Forty',
        'Fifty',
        'Sixty',
        'Seventy',
        'Eighty',
        'Ninety',
    ];

    private static $thousands = [
        '',
        'Thousand',
        'Million',
        'Billion',
        'Trillion',
    ];

    /**
     * Mapping of decimal values to fraction symbols and their properties
     * Each entry contains:
     * - decimal: The decimal value of the fraction
     * - symbol: The Unicode symbol for the fraction (if available)
     * - numerator: The numerator of the fraction
     * - denominator: The denominator of the fraction
     * - tolerance: Optional tolerance for floating-point comparison (for fractions that can't be exactly represented)
     */
    private static $fractions = [
        // Halves
        ['decimal' => 0.5, 'symbol' => '½', 'numerator' => 1, 'denominator' => 2],

        // Quarters
        ['decimal' => 0.25, 'symbol' => '¼', 'numerator' => 1, 'denominator' => 4],
        ['decimal' => 0.75, 'symbol' => '¾', 'numerator' => 3, 'denominator' => 4],

        // Eighths
        ['decimal' => 0.125, 'symbol' => '⅛', 'numerator' => 1, 'denominator' => 8],
        ['decimal' => 0.375, 'symbol' => '⅜', 'numerator' => 3, 'denominator' => 8],
        ['decimal' => 0.625, 'symbol' => '⅝', 'numerator' => 5, 'denominator' => 8],
        ['decimal' => 0.875, 'symbol' => '⅞', 'numerator' => 7, 'denominator' => 8],

        // Thirds
        ['decimal' => 0.333333, 'symbol' => '⅓', 'numerator' => 1, 'denominator' => 3, 'tolerance' => 0.000001],
        ['decimal' => 0.666667, 'symbol' => '⅔', 'numerator' => 2, 'denominator' => 3, 'tolerance' => 0.000001],

        // Fifths
        ['decimal' => 0.2, 'symbol' => '⅕', 'numerator' => 1, 'denominator' => 5],
        ['decimal' => 0.4, 'symbol' => '⅖', 'numerator' => 2, 'denominator' => 5],
        ['decimal' => 0.6, 'symbol' => '⅗', 'numerator' => 3, 'denominator' => 5],
        ['decimal' => 0.8, 'symbol' => '⅘', 'numerator' => 4, 'denominator' => 5],

        // Sixths
        ['decimal' => 0.166667, 'symbol' => '⅙', 'numerator' => 1, 'denominator' => 6, 'tolerance' => 0.000001],
        ['decimal' => 0.833333, 'symbol' => '⅚', 'numerator' => 5, 'denominator' => 6, 'tolerance' => 0.000001],

        // Sevenths
        ['decimal' => 0.142857, 'symbol' => '⅐', 'numerator' => 1, 'denominator' => 7, 'tolerance' => 0.000001],
        ['decimal' => 0.285714, 'symbol' => '²⁄₇', 'numerator' => 2, 'denominator' => 7, 'tolerance' => 0.000001],
        ['decimal' => 0.428571, 'symbol' => '³⁄₇', 'numerator' => 3, 'denominator' => 7, 'tolerance' => 0.000001],
        ['decimal' => 0.571429, 'symbol' => '⁴⁄₇', 'numerator' => 4, 'denominator' => 7, 'tolerance' => 0.000001],
        ['decimal' => 0.714286, 'symbol' => '⁵⁄₇', 'numerator' => 5, 'denominator' => 7, 'tolerance' => 0.000001],
        ['decimal' => 0.857143, 'symbol' => '⁶⁄₇', 'numerator' => 6, 'denominator' => 7, 'tolerance' => 0.000001],

        // Ninths
        ['decimal' => 0.111111, 'symbol' => '¹⁄₉', 'numerator' => 1, 'denominator' => 9, 'tolerance' => 0.000001],
        ['decimal' => 0.888889, 'symbol' => '⁸⁄₉', 'numerator' => 8, 'denominator' => 9, 'tolerance' => 0.000001],

        // Tenths
        ['decimal' => 0.1, 'symbol' => '⅒', 'numerator' => 1, 'denominator' => 10],
        ['decimal' => 0.3, 'symbol' => '³⁄₁₀', 'numerator' => 3, 'denominator' => 10],
        ['decimal' => 0.7, 'symbol' => '⁷⁄₁₀', 'numerator' => 7, 'denominator' => 10],
        ['decimal' => 0.9, 'symbol' => '⁹⁄₁₀', 'numerator' => 9, 'denominator' => 10],
    ];

    public static function convert($number, $currencyUnit = '', $fractionUnit = '')
    {
        // Handle zero case
        if ($number == 0) {
            return 'Zero' . ($currencyUnit ? ' ' . $currencyUnit : '');
        }

        // Split number into integer and decimal parts
        $parts = explode('.', number_format($number, 2, '.', ''));
        $integerPart = $parts[0];
        $decimalPart = $parts[1] ?? '00';

        $words = self::convertInteger(abs($integerPart));
        if ($currencyUnit) {
            $words .= ' ' . $currencyUnit;
        }

        // Handle decimal part
        if ($decimalPart > 0) {
            $decimalWords = self::convertInteger($decimalPart);
            $words .= ' and ' . $decimalWords;
            if ($fractionUnit) {
                $words .= ' ' . $fractionUnit;
            }
        }

        // Handle negative numbers
        if ($number < 0) {
            $words = 'Negative ' . $words;
        }

        return $words;
    }

    /**
     * Convert decimal number to fraction format for print-friendly display
     * Example: 30.5 becomes "30½", 12.25 becomes "12¼", 15.75 becomes "15¾"
     *
     * @param  float|int|null $number The number to convert to fraction format
     * @return string         The number in fraction format
     */
    public static function toFraction($number)
    {
        // Handle null or zero values
        if ($number === null || $number == 0) {
            return '0';
        }

        // Extract integer and decimal parts
        $integerPart = (int) floor(abs($number));
        $decimalPart = abs($number) - $integerPart;

        // Handle negative numbers
        $sign = $number < 0 ? '-' : '';

        // If no decimal part, return integer only
        if ($decimalPart == 0) {
            return "{$sign}{$integerPart}";
        }

        // Round decimal part to 6 decimal places to handle floating point precision
        $decimalPart = round($decimalPart, 6);

        // Try to match with predefined fractions
        foreach (self::$fractions as $fraction) {
            $decimal = $fraction['decimal'];
            $symbol = $fraction['symbol'];

            // Check if the decimal matches the fraction
            if (isset($fraction['tolerance'])) {
                // Use tolerance for approximate matching (e.g., for 1/3, 2/3)
                if (abs($decimalPart - $decimal) < $fraction['tolerance']) {
                    return $integerPart > 0
                        ? "{$sign}{$integerPart}{$symbol}"
                        : "{$sign}{$symbol}";
                }
            } else {
                // Use exact matching for precise fractions (e.g., 1/2, 1/4)
                if ($decimalPart == $decimal) {
                    return $integerPart > 0
                        ? "{$sign}{$integerPart}{$symbol}"
                        : "{$sign}{$symbol}";
                }
            }
        }

        // For decimals that don't match predefined fractions, convert to simplified fraction
        $precision = 1000; // Increased precision for better fraction approximation
        $numerator = (int) round($decimalPart * $precision);
        $denominator = $precision;
        $gcd = self::gcd($numerator, $denominator);
        $numerator = (int) ($numerator / $gcd);
        $denominator = (int) ($denominator / $gcd);

        // Format the result
        if ($integerPart > 0) {
            return "{$sign}{$integerPart} {$numerator}/{$denominator}";
        }

        return "{$sign}{$numerator}/{$denominator}";
    }

    /** Calculate Greatest Common Divisor */
    private static function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }

        return $a;
    }

    private static function convertInteger($number)
    {
        if ($number == 0) {
            return '';
        }

        $words = [];
        $chunkCount = 0;

        while ($number > 0) {
            $chunk = $number % 1000;
            if ($chunk > 0) {
                $chunkWords = self::convertHundreds($chunk);
                if (! empty(self::$thousands[$chunkCount])) {
                    $chunkWords .= ' ' . self::$thousands[$chunkCount];
                }
                array_unshift($words, $chunkWords);
            }
            $number = (int) ($number / 1000);
            $chunkCount++;
        }

        return implode(' ', array_filter($words));
    }

    private static function convertHundreds($number)
    {
        $words = [];

        if ($number >= 100) {
            $words[] = self::$units[(int) ($number / 100)] . ' Hundred';
            $number %= 100;
        }

        if ($number >= 20) {
            $words[] = self::$tens[(int) ($number / 10)];
            $number %= 10;
        }

        if ($number > 0 && $number < 20) {
            $words[] = self::$units[$number];
        }

        return implode(' ', array_filter($words));
    }
}
