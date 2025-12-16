<?php

namespace App\Http\Documentation\Strategies;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;
use ReflectionMethod;

/**
 * This strategy extracts @additional annotations from controller methods
 * and stores them for later use by a custom OpenAPI generator
 */
class ParseAdditionalAnnotation extends Strategy
{
    /** Static storage for additional data */
    protected static array $additionalData = [];

    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): ?array
    {
        // Create a unique key for this endpoint
        $endpointKey = $endpointData->httpMethods[0] . ':' . $endpointData->uri;

        // Get the controller method reflection
        $method = $endpointData->method;

        if (! $method instanceof ReflectionMethod) {
            return null;
        }

        // Get the docblock
        $docblock = $method->getDocComment();

        if (! $docblock) {
            return null;
        }

        // Look for @additional annotation with nested JSON
        if (preg_match('/@additional\s+({.+})/s', $docblock, $matches)) {
            $jsonString = trim($matches[1]);

            try {
                $additionalData = json_decode($jsonString, true);

                if (json_last_error() === JSON_ERROR_NONE && $additionalData) {
                    // Store this information for use by OpenAPI generator
                    self::$additionalData[$endpointKey] = $additionalData;
                }
            } catch (\Exception $e) {
                // Silent fail if JSON parsing fails
            }
        }

        // Response strategies don't directly modify responses
        // We store the data and use it in a custom OpenAPI generator
        return null;
    }

    /** Get additional data for an endpoint */
    public static function getAdditionalData(string $endpointKey): ?array
    {
        return self::$additionalData[$endpointKey] ?? null;
    }

    /** Get all additional data */
    public static function getAllAdditionalData(): array
    {
        return self::$additionalData;
    }

    /** Clear stored data (useful for testing) */
    public static function clearData(): void
    {
        self::$additionalData = [];
    }
}
