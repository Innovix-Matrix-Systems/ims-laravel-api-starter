<?php

namespace App\Http\Documentation\Generators;

use App\Http\Documentation\Strategies\ParseAdditionalAnnotation;
use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\BaseGenerator;

class ImsPreferredAdditionalMetaDataGenerator extends BaseGenerator
{
    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        // Create endpoint key
        $method = strtoupper($endpoint->httpMethods[0]);
        $uri = $endpoint->uri;
        $endpointKey = $method . ':' . $uri;

        // Get additional data for this endpoint
        $additionalData = ParseAdditionalAnnotation::getAdditionalData($endpointKey);

        if (! $additionalData) {
            return $pathItem;
        }

        // Ensure responses exists
        if (! isset($pathItem['responses'])) {
            $pathItem['responses'] = [];
        }

        // Convert stdClass to array if needed
        if ($pathItem['responses'] instanceof \stdClass) {
            $pathItem['responses'] = (array) $pathItem['responses'];
        }

        // Merge additional data into each successful response (200, 201, etc.)
        foreach ($pathItem['responses'] as $statusCode => &$response) {
            // Only modify successful responses
            if (is_numeric($statusCode) && $statusCode >= 200 && $statusCode < 300) {

                // Check if example is at schema level (common for API resources)
                if (isset($response['content']['application/json']['schema']['example'])) {
                    $example = $response['content']['application/json']['schema']['example'];
                    if ($example instanceof \stdClass) {
                        $example = json_decode(json_encode($example), true);
                    }
                    if (is_array($example)) {
                        if (isset($additionalData['meta']) && isset($example['meta']) && is_array($additionalData['meta']) && is_array($example['meta'])) {
                            $example['meta'] = $this->deepMerge($example['meta'], $additionalData['meta']);
                            $tmp = $additionalData;
                            unset($tmp['meta']);
                            $example = array_merge($example, $tmp);
                        } else {
                            $example = array_merge($example, $additionalData);
                        }
                        $response['content']['application/json']['schema']['example'] = $example;
                    }
                }
                // Also check if example is at content level (alternative structure)
                elseif (isset($response['content']['application/json']['example'])) {
                    $example = $response['content']['application/json']['example'];
                    if ($example instanceof \stdClass) {
                        $example = json_decode(json_encode($example), true);
                    }
                    if (is_array($example)) {
                        if (isset($additionalData['meta']) && isset($example['meta']) && is_array($additionalData['meta']) && is_array($example['meta'])) {
                            $example['meta'] = $this->deepMerge($example['meta'], $additionalData['meta']);
                            $tmp = $additionalData;
                            unset($tmp['meta']);
                            $example = array_merge($example, $tmp);
                        } else {
                            $example = array_merge($example, $additionalData);
                        }
                        $response['content']['application/json']['example'] = $example;
                    }
                }

                // Also update the schema properties if present
                if (isset($response['content']['application/json']['schema']['properties'])) {
                    $props = &$response['content']['application/json']['schema']['properties'];
                    if (isset($additionalData['meta']) && isset($props['meta']['properties']) && is_array($additionalData['meta']) && is_array($props['meta']['properties'])) {
                        foreach ($additionalData['meta'] as $k => $v) {
                            $props['meta']['properties'][$k] = $this->generateSchemaForValue($v);
                        }
                    }
                    foreach ($additionalData as $key => $value) {
                        if ($key === 'meta') {
                            continue;
                        }
                        $props[$key] = $this->generateSchemaForValue($value);
                    }
                }
            }
        }

        return $pathItem;
    }

    /** Generate OpenAPI schema for a value */
    protected function generateSchemaForValue($value): array
    {
        if (is_array($value)) {
            // Check if it's an associative array (object) or indexed array
            $isObject = array_keys($value) !== range(0, count($value) - 1);

            if ($isObject) {
                $properties = [];
                foreach ($value as $key => $val) {
                    $properties[$key] = $this->generateSchemaForValue($val);
                }

                return [
                    'type' => 'object',
                    'properties' => $properties,
                ];
            }
            // It's an array
            $items = ! empty($value) ? $this->generateSchemaForValue($value[0]) : ['type' => 'string'];

            return [
                'type' => 'array',
                'items' => $items,
            ];

        }

        if (is_int($value)) {
            return ['type' => 'integer', 'example' => $value];
        }

        if (is_float($value)) {
            return ['type' => 'number', 'example' => $value];
        }

        if (is_bool($value)) {
            return ['type' => 'boolean', 'example' => $value];
        }

        if (is_null($value)) {
            return ['type' => 'string', 'nullable' => true, 'example' => null];
        }

        return ['type' => 'string', 'example' => (string) $value];
    }

    protected function deepMerge(array $base, array $overlay): array
    {
        foreach ($overlay as $key => $value) {
            if (isset($base[$key]) && is_array($base[$key]) && is_array($value)) {
                $isAssoc = array_keys($value) !== range(0, count($value) - 1);
                $isAssocBase = array_keys($base[$key]) !== range(0, count($base[$key]) - 1);
                if ($isAssoc && $isAssocBase) {
                    $base[$key] = $this->deepMerge($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            } else {
                $base[$key] = $value;
            }
        }

        return $base;
    }
}
