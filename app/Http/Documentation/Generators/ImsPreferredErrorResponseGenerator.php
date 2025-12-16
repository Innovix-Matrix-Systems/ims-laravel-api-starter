<?php

namespace App\Http\Documentation\Generators;

use Knuckles\Camel\Output\OutputEndpointData;
use Knuckles\Scribe\Writing\OpenApiSpecGenerators\BaseGenerator;

class ImsPreferredErrorResponseGenerator extends BaseGenerator
{
    public function pathItem(array $pathItem, array $groupedEndpoints, OutputEndpointData $endpoint): array
    {
        // Ensure responses exists
        if (! isset($pathItem['responses'])) {
            $pathItem['responses'] = [];
        }

        // Convert stdClass to array if needed
        if ($pathItem['responses'] instanceof \stdClass) {
            $pathItem['responses'] = (array) $pathItem['responses'];
        }

        $authenticated = ($endpoint->metadata['authenticated'] ?? true) === true;

        $method = strtoupper($endpoint->httpMethods[0]);

        if ($authenticated) {
            if (! isset($pathItem['responses']['401'])) {
                $pathItem['responses']['401'] = [
                    'description' => 'Unauthenticated.',
                    'content' => [
                        'application/json' => [
                            'example' => ['message' => 'Unauthenticated.'],
                        ],
                    ],
                ];
            }
        }

        if ($authenticated) {
            if (! isset($pathItem['responses']['403'])) {
                $pathItem['responses']['403'] = [
                    'description' => 'Forbidden.',
                    'content' => [
                        'application/json' => [
                            'example' => ['message' => 'This action is unauthorized.'],
                        ],
                    ],
                ];
            }
        }

        // 422
        if (! in_array($method, ['POST', 'PUT', 'PATCH'])) {
            return $pathItem;
        }
        $errors = [];

        // Build error messages from required body params
        if (! empty($endpoint->bodyParameters)) {
            foreach ($endpoint->bodyParameters as $paramName => $param) {
                if (($param['required'] ?? false) === true) {
                    $errors[$paramName] = ["The {$paramName} field is required."];
                }
            }
        }

        // Fallback if no params or no required params found
        if (empty($errors)) {
            $errors = ['field' => ['This field is required.']];
        }

        $pathItem['responses']['422'] = [
            'description' => 'Validation error. Unprocessable Entity.',
            'content' => [
                'application/json' => [
                    'example' => [
                        'message' => 'The given data was invalid.',
                        'errors' => $errors,
                    ],
                ],
            ],
        ];

        // 500
        if (! isset($pathItem['responses']['500'])) {
            $pathItem['responses']['500'] = [
                'description' => 'Internal Server Error.',
                'content' => [
                    'application/json' => [
                        'example' => [
                            'message' => 'Internal Server Error. Please try again later.',
                        ],
                    ],
                ],
            ];
        }

        return $pathItem;
    }
}
