<?php

namespace App\Exceptions;

use App\Enums\ApiErrorCode;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * RFC 9457 Compliant API Exception
 *
 * This exception class implements the Problem Details for HTTP APIs standard (RFC 9457).
 * It provides a standardized format for API error responses with different detail levels
 * based on the application environment (debug mode).
 *
 * @link https://www.rfc-editor.org/rfc/rfc9457.html RFC 9457 Specification
 *
 * Usage Examples:
 *
 * throw new ApiException(
 *     responseCode: Response::HTTP_UNPROCESSABLE_ENTITY,
 *     errorCode: ApiErrorCode::INSUFFICIENT_BALANCE,
 *     errorMessage: 'Insufficient balance to complete this transaction',
 *     errorTitle: 'Business Logic Validation Failed'
 * );
 *
 * throw new ApiException(
 *     responseCode: Response::HTTP_INTERNAL_SERVER_ERROR,
 *     errorCode: ApiErrorCode::DATABASE_CONNECTION_FAILED,
 *     errorMessage: 'Unable to establish database connection',
 *     errorTitle: 'Internal Server Error',
 *     additionalData: ['retry_after' => 60]
 * );
 *
 * throw ApiException::businessLogicError(
 *   ApiErrorCode::INSUFFICIENT_STOCK->value,
 *   'Only 5 items available, but you requested 10'
 * );
 *
 * throw ApiException::resourceNotFound('Branch', 123);
 *
 * throw ApiException::unauthorized();
 *
 * throw ApiException::forbidden();
 */
class ApiException extends Exception
{
    private int $responseCode;
    private string $errorCode;
    private string $errorMessage;
    private ?string $errorTitle;
    private array $additionalData;

    /**
     * Create a new API Exception instance
     *
     * @param int            $responseCode   HTTP status code (use Response::HTTP_* constants)
     * @param string         $errorCode      Application-specific error code (use ApiErrorCode constants)
     * @param string         $errorMessage   Human-readable error message with details
     * @param string|null    $errorTitle     Short, human-readable summary of the problem type
     * @param array          $additionalData Additional contextual data to include in response
     * @param Throwable|null $previous       Previous exception for exception chaining
     */
    public function __construct(
        int $responseCode,
        string $errorCode,
        string $errorMessage,
        ?string $errorTitle = null,
        array $additionalData = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($errorMessage, 0, $previous);

        $this->responseCode = $responseCode;
        $this->errorCode = $errorCode;
        $this->errorMessage = $errorMessage;
        $this->errorTitle = $errorTitle;
        $this->additionalData = $additionalData;
    }

    /**
     * Static factory methods for common error scenarios
     */

    /**
     * Create a business logic validation error (422)
     *
     * @param string $errorCode      Application error code
     * @param string $message        Error message
     * @param array  $additionalData Optional additional context
     */
    public static function businessLogicError(
        string $errorCode,
        string $message,
        array $additionalData = []
    ): static {
        return new static(
            responseCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            errorCode: $errorCode,
            errorMessage: $message,
            errorTitle: 'Business Logic Validation Failed',
            additionalData: $additionalData
        );
    }

    /**
     * Create a resource not found error (404)
     *
     * @param string     $resourceType Type of resource (e.g., 'Branch', 'User')
     * @param string|int $resourceId   Resource identifier
     */
    public static function resourceNotFound(
        string $resourceType,
        string|int $resourceId
    ): static {
        return new static(
            responseCode: Response::HTTP_NOT_FOUND,
            errorCode: ApiErrorCode::RESOURCE_NOT_FOUND->value,
            errorMessage: "{$resourceType} with ID '{$resourceId}' was not found",
            errorTitle: 'Resource Not Found'
        );
    }

    /**
     * Create an unauthorized error (401)
     *
     * @param string $message Optional custom message
     */
    public static function unauthorized(string $message = 'Authentication required'): static
    {
        return new static(
            responseCode: Response::HTTP_UNAUTHORIZED,
            errorCode: ApiErrorCode::UNAUTHORIZED->value,
            errorMessage: $message,
            errorTitle: 'Unauthorized Access'
        );
    }

    /**
     * Create a forbidden error (403)
     *
     * @param string $message Optional custom message
     */
    public static function forbidden(string $message = 'You do not have permission to access this resource'): static
    {
        return new static(
            responseCode: Response::HTTP_FORBIDDEN,
            errorCode: ApiErrorCode::FORBIDDEN->value,
            errorMessage: $message,
            errorTitle: 'Access Forbidden'
        );
    }

    /**
     * Create a bad request error (400)
     *
     * @param string $errorCode Application error code
     * @param string $message   Error message
     */
    public static function badRequest(string $errorCode, string $message): static
    {
        return new static(
            responseCode: Response::HTTP_BAD_REQUEST,
            errorCode: $errorCode,
            errorMessage: $message,
            errorTitle: 'Bad Request'
        );
    }

    /**
     * Create an internal server error (500)
     *
     * @param string         $errorCode Application error code
     * @param string         $message   Error message
     * @param Throwable|null $previous  Previous exception for debugging
     */
    public static function serverError(
        string $errorCode,
        string $message,
        ?Throwable $previous = null
    ): static {
        return new static(
            responseCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            errorCode: $errorCode,
            errorMessage: $message,
            errorTitle: 'Internal Server Error',
            previous: $previous
        );
    }

    /**
     * Create a service unavailable error (503)
     *
     * @param string   $message    Error message
     * @param int|null $retryAfter Seconds until service should be available
     */
    public static function serviceUnavailable(
        string $message = 'Service temporarily unavailable',
        ?int $retryAfter = null
    ): static {
        $additionalData = [];
        if ($retryAfter !== null) {
            $additionalData['retry_after'] = $retryAfter;
        }

        return new static(
            responseCode: Response::HTTP_SERVICE_UNAVAILABLE,
            errorCode: ApiErrorCode::SERVICE_UNAVAILABLE->value,
            errorMessage: $message,
            errorTitle: 'Service Unavailable',
            additionalData: $additionalData
        );
    }

    /**
     * Create a rate limit error (429)
     *
     * @param int $retryAfter Seconds until rate limit resets
     */
    public static function rateLimitExceeded(int $retryAfter = 60): static
    {
        return new static(
            responseCode: Response::HTTP_TOO_MANY_REQUESTS,
            errorCode: ApiErrorCode::RATE_LIMIT_EXCEEDED->value,
            errorMessage: 'Too many requests. Please try again later.',
            errorTitle: 'Rate Limit Exceeded',
            additionalData: ['retry_after' => $retryAfter]
        );
    }

    /**
     * Create a conflict error (409)
     *
     * @param string $errorCode Application error code
     * @param string $message   Error message
     */
    public static function conflict(string $errorCode, string $message): static
    {
        return new static(
            responseCode: Response::HTTP_CONFLICT,
            errorCode: $errorCode,
            errorMessage: $message,
            errorTitle: 'Conflict'
        );
    }

    /**
     * Create a method not allowed error (405)
     *
     * @param string $message Error message
     */
    public static function methodNotAllowed(string $message = 'The HTTP method is not allowed for this endpoint'): static
    {
        return new static(
            responseCode: Response::HTTP_METHOD_NOT_ALLOWED,
            errorCode: ApiErrorCode::METHOD_NOT_ALLOWED->value,
            errorMessage: $message,
            errorTitle: 'Method Not Allowed'
        );
    }

    /**
     * Report or log the exception
     *
     * Logs different severity levels based on HTTP status code:
     * - 5xx: Critical/Error (server-side issues)
     * - 4xx: Warning (client-side issues)
     */
    public function report(): void
    {
        $logContext = [
            'error_code' => $this->errorCode,
            'http_status' => $this->responseCode,
            'message' => $this->errorMessage,
            'additional_data' => $this->additionalData,
        ];

        if ($this->responseCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            Log::error('API Exception (Server Error)', $logContext);

            if (config('app.debug')) {
                Log::error('Stack Trace', ['trace' => $this->getTraceAsString()]);
            }
        } else {
            Log::warning('API Exception (Client Error)', $logContext);
        }
    }

    /**
     * Render the exception into an HTTP response
     *
     * Returns RFC 9457 compliant JSON response with different detail levels:
     * - Debug Mode (APP_DEBUG=true): Includes detailed error information, stack trace, file location
     * - Production Mode (APP_DEBUG=false): Returns minimal, safe error information
     *
     * @param \Illuminate\Http\Request $request
     */
    public function render($request): JsonResponse
    {
        $isDebugMode = config('app.debug', false);

        // Build RFC 9457 compliant response
        $response = [
            'type' => $this->buildErrorTypeUri(),
            'title' => $this->getErrorTitle(),
            'detail' => $this->getErrorDetail($isDebugMode),
            'instance' => $this->getRequestInstance($request),
        ];

        // Add application-specific error code (RFC 9457 allows extensions)
        $response['error_code'] = $this->errorCode;

        // Add timestamp for debugging and logging correlation
        $response['timestamp'] = now()->toIso8601String();

        // Include status in response body only for 5xx errors (for logging/persistence)
        if ($this->responseCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            $response['status'] = $this->responseCode;
            $response['trace_id'] = $this->generateTraceId();
        }

        // Merge any additional contextual data
        if (! empty($this->additionalData)) {
            $response = array_merge($response, $this->additionalData);
        }

        // Add debug information in development environment
        if ($isDebugMode) {
            $response['debug'] = $this->getDebugInformation();
        }

        return new JsonResponse($response, $this->responseCode);
    }

    /**
     * Build the error type URI (RFC 9457 'type' field)
     *
     * Creates a URI that identifies the error type. In production, this could
     * link to your API documentation explaining the error.
     */
    private function buildErrorTypeUri(): string
    {
        $baseUrl = config('app.url');
        $errorCodeSlug = strtolower(str_replace('_', '-', $this->errorCode));

        return "{$baseUrl}/errors/{$errorCodeSlug}";
    }

    /**
     * Get the error title (RFC 9457 'title' field)
     *
     * Returns a short, human-readable summary. If not provided,
     * uses Symfony's status text mapping.
     */
    private function getErrorTitle(): string
    {
        if ($this->errorTitle !== null) {
            return $this->errorTitle;
        }

        // Use Symfony's status text mapping
        return Response::$statusTexts[$this->responseCode] ?? 'Error';
    }

    /** Get the error detail message (RFC 9457 'detail' field) */
    private function getErrorDetail(bool $isDebugMode): string
    {
        // In production, sanitize 5xx errors to avoid exposing internal details
        if (! $isDebugMode && $this->responseCode >= Response::HTTP_INTERNAL_SERVER_ERROR) {
            return 'An unexpected error occurred while processing your request. Please try again later.';
        }

        return $this->errorMessage;
    }

    /**
     * Get the request instance path (RFC 9457 'instance' field)
     *
     * @param \Illuminate\Http\Request $request
     */
    private function getRequestInstance($request): string
    {
        return '/' . ltrim($request->path(), '/');
    }

    /**
     * Generate a unique trace ID for error tracking
     *
     * Useful for correlating errors across logs, monitoring systems,
     * and support requests.
     */
    private function generateTraceId(): string
    {
        return 'err_' . bin2hex(random_bytes(8)) . '_' . time();
    }

    /** Get detailed debug information (only in debug mode) */
    private function getDebugInformation(): array
    {
        $debug = [
            'exception_class' => get_class($this),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => collect($this->getTrace())->map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? 'unknown',
                    'line' => $trace['line'] ?? 0,
                    'function' => $trace['function'] ?? 'unknown',
                    'class' => $trace['class'] ?? null,
                ];
            })->take(10)->toArray(), // Limit trace to 10 frames
        ];

        // Include previous exception if exists
        if ($this->getPrevious() !== null) {
            $previous = $this->getPrevious();
            $debug['previous_exception'] = [
                'class' => get_class($previous),
                'message' => $previous->getMessage(),
                'file' => $previous->getFile(),
                'line' => $previous->getLine(),
            ];
        }

        return $debug;
    }
}
