<?php

namespace App\Http\Documentation\Strategies;

use Knuckles\Camel\Extraction\ExtractedEndpointData;
use Knuckles\Scribe\Extracting\Strategies\Strategy;

class OptionalFileUploadDetector extends Strategy
{
    /** Static storage for endpoint information */
    protected static array $endpointInfo = [];

    public function __invoke(ExtractedEndpointData $endpointData, array $routeRules = []): ?array
    {
        // This strategy doesn't extract new data, but modifies existing endpoint data
        // to indicate when both application/json and multipart/form-data should be supported

        // Create a unique key for this endpoint
        $endpointKey = $endpointData->httpMethods[0] . ':' . $endpointData->uri;

        // Check if this endpoint has a FormRequest that allows optional file uploads
        $formRequest = $this->getFormRequestClass($endpointData);

        if ($formRequest && class_exists($formRequest)) {
            // Check if the form request has optional file upload rules
            try {
                $requestInstance = new $formRequest;
                if (method_exists($requestInstance, 'rules')) {
                    $rules = $requestInstance->rules();

                    $hasOptionalFileUpload = false;
                    $optionalFileFields = [];
                    $allFields = [];

                    foreach ($rules as $field => $ruleSet) {
                        $rulesArray = is_string($ruleSet) ? explode('|', $ruleSet) : (array) $ruleSet;

                        // Store all fields and their rules for example generation
                        $allFields[$field] = [
                            'rules' => $rulesArray,
                            'required' => in_array('required', $rulesArray),
                        ];

                        // Check if this is an optional file upload field
                        $isFileField = in_array('image', $rulesArray) ||
                                      in_array('file', $rulesArray) ||
                                      in_array('mimes', $rulesArray);

                        $isRequired = in_array('required', $rulesArray);
                        $isSometimes = in_array('sometimes', $rulesArray);

                        // A file field is optional if:
                        // 1. It's a file field AND
                        // 2. It's NOT required OR it uses 'sometimes' rule
                        if ($isFileField && (! $isRequired || $isSometimes)) {
                            $hasOptionalFileUpload = true;
                            $optionalFileFields[] = $field;
                        }
                    }

                    if ($hasOptionalFileUpload) {
                        // Store this information for use in our OpenAPI generator
                        self::$endpointInfo[$endpointKey] = [
                            'allowsOptionalFileUpload' => true,
                            'optionalFileFields' => $optionalFileFields,
                            'allFields' => $allFields,
                        ];
                    }
                }

                // Also check if the form request has body parameters for examples
                if (method_exists($requestInstance, 'bodyParameters')) {
                    $bodyParameters = $requestInstance->bodyParameters();
                    if (! isset(self::$endpointInfo[$endpointKey])) {
                        self::$endpointInfo[$endpointKey] = [
                            'allowsOptionalFileUpload' => false,
                            'bodyParameters' => $bodyParameters,
                        ];
                    } else {
                        self::$endpointInfo[$endpointKey]['bodyParameters'] = $bodyParameters;
                    }
                }
            } catch (\Throwable $e) {
                // If we can't instantiate the request, just continue
            }
        }

        return null; // This strategy doesn't return new data, just modifies existing
    }

    /** Get endpoint information */
    public static function getEndpointInfo(string $endpointKey): ?array
    {
        return self::$endpointInfo[$endpointKey] ?? null;
    }

    /** Extract FormRequest class name from the endpoint */
    protected function getFormRequestClass(ExtractedEndpointData $endpointData): ?string
    {
        // Get the controller method
        $method = $endpointData->method;

        // Get the method parameters
        $parameters = $method->getParameters();

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type && $type instanceof \ReflectionNamedType) {
                $className = $type->getName();
                // Check if this is a FormRequest class
                if (class_exists($className) &&
                    is_subclass_of($className, \Illuminate\Foundation\Http\FormRequest::class)) {
                    return $className;
                }
            }
        }

        return null;
    }
}
