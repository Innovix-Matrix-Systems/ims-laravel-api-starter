<?php

namespace App\Http\Documentation\Generators;

use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\BaseGenerator;

class ImsPreferredOpenApiGenerator extends BaseGenerator
{
    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        // Call parent implementation first
        $pathItem = parent::pathItem($pathItem, $groupedEndpoints, $endpoint);

        // Ensure parameters array exists
        if (! isset($pathItem['parameters'])) {
            $pathItem['parameters'] = [];
        }

        // Check if Accept header already exists
        $hasAccept = false;
        foreach ($pathItem['parameters'] as $param) {
            if (isset($param['name']) && $param['name'] === 'Accept') {
                $hasAccept = true;
                break;
            }
        }

        // Add Accept header if it doesn't exist
        if (! $hasAccept) {
            $pathItem['parameters'][] = [
                'name' => 'Accept',
                'in' => 'header',
                'description' => 'Expected response format',
                'required' => true,
                'schema' => [
                    'type' => 'string',
                    'default' => 'application/json',
                ],
            ];
        }

        // For endpoints with request bodies, check if they already have a Content-Type
        $hasContentType = false;
        foreach ($pathItem['parameters'] as $param) {
            if (isset($param['name']) && $param['name'] === 'Content-Type') {
                $hasContentType = true;
                break;
            }
        }

        // Only add Content-Type header if it doesn't already exist
        // And only for endpoints that can have request bodies (POST, PUT, PATCH)
        if (! $hasContentType && in_array(strtoupper($endpoint->httpMethods[0]), ['POST', 'PUT', 'PATCH'])) {
            // Create a unique key for this endpoint
            $endpointKey = $endpoint->httpMethods[0] . ':' . $endpoint->uri;

            // Check if this endpoint allows optional file uploads
            $endpointInfo = \App\Http\Documentation\Strategies\OptionalFileUploadDetector::getEndpointInfo($endpointKey);
            $allowsOptionalFileUpload = $endpointInfo['allowsOptionalFileUpload'] ?? false;

            // If it allows optional file uploads, we don't add a Content-Type header
            // because both application/json and multipart/form-data are supported
            if (! $allowsOptionalFileUpload) {
                // Default behavior for endpoints that don't allow optional file uploads
                // Check if this endpoint has specific Content-Type requirements (e.g., multipart/form-data)
                $contentTypeToAdd = 'application/json';

                // Look for existing Content-Type in request body specification
                if (isset($pathItem['requestBody']['content'])) {
                    $contentTypes = array_keys($pathItem['requestBody']['content']);
                    // If there's only one content type and it's not application/json, use that instead
                    if (count($contentTypes) === 1 && $contentTypes[0] !== 'application/json') {
                        $contentTypeToAdd = $contentTypes[0];
                    }
                }

                $pathItem['parameters'][] = [
                    'name' => 'Content-Type',
                    'in' => 'header',
                    'description' => 'Request body format',
                    'required' => true,
                    'schema' => [
                        'type' => 'string',
                        'default' => $contentTypeToAdd,
                    ],
                ];
            }
        }

        return $pathItem;
    }

    /** Override the generateEndpointRequestBodySpec method to handle endpoints with optional file uploads */
    protected function generateEndpointRequestBodySpec(OutputEndpointData $endpoint): array|\stdClass
    {
        // Call parent implementation first
        $body = parent::generateEndpointRequestBodySpec($endpoint);

        // Create a unique key for this endpoint
        $endpointKey = $endpoint->httpMethods[0] . ':' . $endpoint->uri;

        // Check if this endpoint allows optional file uploads
        $endpointInfo = \App\Http\Documentation\Strategies\OptionalFileUploadDetector::getEndpointInfo($endpointKey);
        $allowsOptionalFileUpload = $endpointInfo['allowsOptionalFileUpload'] ?? false;

        // If it allows optional file uploads, we should support both content types
        if ($allowsOptionalFileUpload && isset($body['content'])) {
            // If we only have multipart/form-data, add application/json as well
            if (count($body['content']) == 1 && isset($body['content']['multipart/form-data'])) {
                // Get the schema from multipart content
                $multipartSchema = $body['content']['multipart/form-data']['schema'] ?? [];

                // Create a JSON version of the schema by removing file-specific properties
                $jsonSchema = $this->convertMultipartSchemaToJsonSchema($multipartSchema, $endpointInfo);

                // Add application/json as an alternative content type
                $body['content']['application/json'] = [
                    'schema' => $jsonSchema,
                    'example' => $this->generateJsonExample($endpoint, $endpointInfo),
                ];

                // Also enhance the multipart form-data with better examples
                $body['content']['multipart/form-data']['example'] = $this->generateMultipartExample($endpoint, $endpointInfo);
            }
        }

        return $body;
    }

    /** Convert a multipart schema to a JSON schema by removing file-specific properties */
    protected function convertMultipartSchemaToJsonSchema(array $multipartSchema, array $endpointInfo): array
    {
        $jsonSchema = $multipartSchema;

        // Remove file-specific properties from the schema
        if (isset($jsonSchema['properties'])) {
            foreach ($jsonSchema['properties'] as $name => $property) {
                if (isset($property['type']) && $property['type'] === 'string' && isset($property['format']) && $property['format'] === 'binary') {
                    // Convert binary file fields to string fields for JSON
                    $jsonSchema['properties'][$name]['format'] = null;
                    // Check if we have body parameters with example data
                    if (isset($endpointInfo['bodyParameters'][$name]['example'])) {
                        $jsonSchema['properties'][$name]['example'] = $endpointInfo['bodyParameters'][$name]['example'];
                    } else {
                        $jsonSchema['properties'][$name]['example'] = 'path/to/file.txt';
                    }
                }
            }
        }

        return $jsonSchema;
    }

    /** Generate a JSON example for the endpoint */
    protected function generateJsonExample(OutputEndpointData $endpoint, array $endpointInfo): array
    {
        $example = [];

        // Use body parameters if available
        if (isset($endpointInfo['bodyParameters'])) {
            foreach ($endpointInfo['bodyParameters'] as $name => $parameter) {
                // Skip file parameters for JSON example
                if (isset($parameter['description']) && stripos($parameter['description'], 'file') !== false) {
                    continue;
                }

                $example[$name] = $parameter['example'] ?? null;
            }
        } else {
            // Fallback to body parameters from endpoint
            foreach ($endpoint->bodyParameters as $name => $parameter) {
                // Skip file parameters for JSON example
                if (isset($parameter['type']) && $parameter['type'] === 'file') {
                    continue;
                }

                $example[$name] = $parameter['example'] ?? null;
            }
        }

        return $example;
    }

    /** Generate a multipart example for the endpoint */
    protected function generateMultipartExample(OutputEndpointData $endpoint, array $endpointInfo): array
    {
        $example = [];

        // Use body parameters if available
        if (isset($endpointInfo['bodyParameters'])) {
            foreach ($endpointInfo['bodyParameters'] as $name => $parameter) {
                $example[$name] = $parameter['example'] ?? null;
            }
        } else {
            // Fallback to body parameters from endpoint
            foreach ($endpoint->bodyParameters as $name => $parameter) {
                $example[$name] = $parameter['example'] ?? null;
            }
        }

        return $example;
    }
}
