<?php

namespace App\Enums;

/**
 * API Error Code Constants
 *
 * Centralized enum for common, cross-cutting API error codes.
 * These are infrastructure and framework-level errors that apply across all domains.
 *
 * Business/domain-specific errors should be defined in their respective services as constants.
 * For example:
 * - UserService::ERR_EMAIL_EXISTS
 * - OrderService::ERR_INSUFFICIENT_STOCK
 * - PaymentService::ERR_PAYMENT_DECLINED
 *
 * This approach keeps domain logic decoupled while maintaining consistency
 * for common API-level errors.
 *
 * Usage:
 * throw new ApiException(
 *     Response::HTTP_UNAUTHORIZED,
 *     ApiErrorCode::UNAUTHORIZED->value,
 *     'Authentication required'
 * );
 *
 * Or use the enum in PHP 8.1+:
 * throw ApiException::unauthorized();
 */
enum ApiErrorCode: string
{
    // ============================================================================
    // AUTHENTICATION & AUTHORIZATION ERRORS (4xx)
    // ============================================================================

    case UNAUTHORIZED = 'UNAUTHORIZED';
    case FORBIDDEN = 'FORBIDDEN';
    case INVALID_CREDENTIALS = 'INVALID_CREDENTIALS';
    case TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    case TOKEN_INVALID = 'TOKEN_INVALID';
    case TOKEN_MISSING = 'TOKEN_MISSING';
    case INSUFFICIENT_PERMISSIONS = 'INSUFFICIENT_PERMISSIONS';
    case SESSION_EXPIRED = 'SESSION_EXPIRED';

    // ============================================================================
    // RESOURCE ERRORS (4xx)
    // ============================================================================

    case RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    case RESOURCE_ALREADY_EXISTS = 'RESOURCE_ALREADY_EXISTS';
    case RESOURCE_DELETED = 'RESOURCE_DELETED';
    case RESOURCE_LOCKED = 'RESOURCE_LOCKED';

    // ============================================================================
    // VALIDATION ERRORS (4xx)
    // ============================================================================

    case VALIDATION_FAILED = 'VALIDATION_FAILED';
    case INVALID_INPUT = 'INVALID_INPUT';
    case MISSING_REQUIRED_FIELD = 'MISSING_REQUIRED_FIELD';
    case INVALID_FORMAT = 'INVALID_FORMAT';
    case INVALID_DATA_TYPE = 'INVALID_DATA_TYPE';

    // ============================================================================
    // RATE LIMITING & THROTTLING (4xx)
    // ============================================================================

    case RATE_LIMIT_EXCEEDED = 'RATE_LIMIT_EXCEEDED';
    case QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
    case CONCURRENT_REQUEST_LIMIT = 'CONCURRENT_REQUEST_LIMIT';

    // ============================================================================
    // HTTP METHOD & REQUEST ERRORS (4xx)
    // ============================================================================

    case METHOD_NOT_ALLOWED = 'METHOD_NOT_ALLOWED';
    case INVALID_REQUEST = 'INVALID_REQUEST';
    case MALFORMED_REQUEST = 'MALFORMED_REQUEST';
    case MALFORMED_JSON = 'MALFORMED_JSON';
    case UNSUPPORTED_MEDIA_TYPE = 'UNSUPPORTED_MEDIA_TYPE';
    case REQUEST_ENTITY_TOO_LARGE = 'REQUEST_ENTITY_TOO_LARGE';
    case URI_TOO_LONG = 'URI_TOO_LONG';

    // ============================================================================
    // CONFLICT & STATE ERRORS (4xx)
    // ============================================================================

    case CONFLICT = 'CONFLICT';
    case PRECONDITION_FAILED = 'PRECONDITION_FAILED';
    case OPERATION_NOT_ALLOWED = 'OPERATION_NOT_ALLOWED';
    case INVALID_STATE = 'INVALID_STATE';
    case GONE = 'GONE';

    // ============================================================================
    // DATABASE ERRORS (5xx)
    // ============================================================================

    case DATABASE_ERROR = 'DATABASE_ERROR';
    case DATABASE_CONNECTION_FAILED = 'DATABASE_CONNECTION_FAILED';
    case QUERY_FAILED = 'QUERY_FAILED';
    case TRANSACTION_FAILED = 'TRANSACTION_FAILED';
    case DEADLOCK_DETECTED = 'DEADLOCK_DETECTED';

    // ============================================================================
    // EXTERNAL SERVICE ERRORS (5xx)
    // ============================================================================

    case EXTERNAL_SERVICE_ERROR = 'EXTERNAL_SERVICE_ERROR';
    case EXTERNAL_SERVICE_TIMEOUT = 'EXTERNAL_SERVICE_TIMEOUT';
    case EXTERNAL_SERVICE_UNAVAILABLE = 'EXTERNAL_SERVICE_UNAVAILABLE';

    // ============================================================================
    // SYSTEM ERRORS (5xx)
    // ============================================================================

    case INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    case SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
    case MAINTENANCE_MODE = 'MAINTENANCE_MODE';
    case CONFIGURATION_ERROR = 'CONFIGURATION_ERROR';
    case NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';

    // ============================================================================
    // FILE & STORAGE ERRORS (5xx)
    // ============================================================================

    case FILE_SYSTEM_ERROR = 'FILE_SYSTEM_ERROR';
    case FILE_NOT_FOUND = 'FILE_NOT_FOUND';
    case FILE_UPLOAD_FAILED = 'FILE_UPLOAD_FAILED';
    case STORAGE_QUOTA_EXCEEDED = 'STORAGE_QUOTA_EXCEEDED';
    case STORAGE_SERVICE_ERROR = 'STORAGE_SERVICE_ERROR';

    // ============================================================================
    // PROCESSING ERRORS (5xx)
    // ============================================================================

    case PROCESSING_ERROR = 'PROCESSING_ERROR';
    case TIMEOUT = 'TIMEOUT';
    case QUEUE_ERROR = 'QUEUE_ERROR';
    case CACHE_ERROR = 'CACHE_ERROR';
    case JOB_FAILED = 'JOB_FAILED';

    /** Get a human-readable description of the error code */
    public function description(): string
    {
        return match ($this) {
            // Authentication & Authorization
            self::UNAUTHORIZED => 'Authentication is required to access this resource',
            self::FORBIDDEN => 'You do not have permission to access this resource',
            self::INVALID_CREDENTIALS => 'The provided credentials are invalid',
            self::TOKEN_EXPIRED => 'The authentication token has expired',
            self::TOKEN_INVALID => 'The authentication token is invalid',
            self::TOKEN_MISSING => 'Authentication token is missing',
            self::INSUFFICIENT_PERMISSIONS => 'Your account does not have sufficient permissions',
            self::SESSION_EXPIRED => 'Your session has expired',

            // Resources
            self::RESOURCE_NOT_FOUND => 'The requested resource was not found',
            self::RESOURCE_ALREADY_EXISTS => 'A resource with these details already exists',
            self::RESOURCE_DELETED => 'This resource has been deleted',
            self::RESOURCE_LOCKED => 'This resource is currently locked',

            // Validation
            self::VALIDATION_FAILED => 'The request data failed validation',
            self::INVALID_INPUT => 'The provided input is invalid',
            self::MISSING_REQUIRED_FIELD => 'A required field is missing',
            self::INVALID_FORMAT => 'The data format is invalid',
            self::INVALID_DATA_TYPE => 'The data type is invalid',

            // Rate Limiting
            self::RATE_LIMIT_EXCEEDED => 'Too many requests, please try again later',
            self::QUOTA_EXCEEDED => 'Your usage quota has been exceeded',
            self::CONCURRENT_REQUEST_LIMIT => 'Too many concurrent requests',

            // HTTP Errors
            self::METHOD_NOT_ALLOWED => 'The HTTP method is not allowed for this endpoint',
            self::INVALID_REQUEST => 'The request is invalid',
            self::MALFORMED_REQUEST => 'The request is malformed',
            self::MALFORMED_JSON => 'The JSON payload is malformed',
            self::UNSUPPORTED_MEDIA_TYPE => 'The media type is not supported',
            self::REQUEST_ENTITY_TOO_LARGE => 'The request payload is too large',
            self::URI_TOO_LONG => 'The request URI is too long',

            // Conflicts
            self::CONFLICT => 'The request conflicts with the current state',
            self::PRECONDITION_FAILED => 'A precondition for this operation failed',
            self::OPERATION_NOT_ALLOWED => 'This operation is not allowed',
            self::INVALID_STATE => 'The resource is in an invalid state',
            self::GONE => 'The resource is no longer available',

            // Database
            self::DATABASE_ERROR => 'A database error occurred',
            self::DATABASE_CONNECTION_FAILED => 'Failed to connect to the database',
            self::QUERY_FAILED => 'Database query failed',
            self::TRANSACTION_FAILED => 'Database transaction failed',
            self::DEADLOCK_DETECTED => 'Database deadlock detected',

            // External Services
            self::EXTERNAL_SERVICE_ERROR => 'An external service error occurred',
            self::EXTERNAL_SERVICE_TIMEOUT => 'External service request timed out',
            self::EXTERNAL_SERVICE_UNAVAILABLE => 'External service is unavailable',

            // System
            self::INTERNAL_SERVER_ERROR => 'An internal server error occurred',
            self::SERVICE_UNAVAILABLE => 'The service is temporarily unavailable',
            self::MAINTENANCE_MODE => 'The system is currently under maintenance',
            self::CONFIGURATION_ERROR => 'A configuration error occurred',
            self::NOT_IMPLEMENTED => 'This feature is not yet implemented',

            // File & Storage
            self::FILE_SYSTEM_ERROR => 'File system error occurred',
            self::FILE_NOT_FOUND => 'The requested file was not found',
            self::FILE_UPLOAD_FAILED => 'File upload failed',
            self::STORAGE_QUOTA_EXCEEDED => 'Storage quota has been exceeded',
            self::STORAGE_SERVICE_ERROR => 'Storage service error occurred',

            // Processing
            self::PROCESSING_ERROR => 'An error occurred while processing the request',
            self::TIMEOUT => 'The operation timed out',
            self::QUEUE_ERROR => 'Queue processing error occurred',
            self::CACHE_ERROR => 'Cache operation error occurred',
            self::JOB_FAILED => 'Background job failed',
        };
    }

    /** Get the recommended HTTP status code for this error */
    public function httpStatusCode(): int
    {
        return match ($this) {
            // 400 Bad Request
            self::INVALID_REQUEST,
            self::MALFORMED_REQUEST,
            self::MALFORMED_JSON,
            self::INVALID_INPUT,
            self::INVALID_FORMAT,
            self::INVALID_DATA_TYPE => 400,

            // 401 Unauthorized
            self::UNAUTHORIZED,
            self::INVALID_CREDENTIALS,
            self::TOKEN_EXPIRED,
            self::TOKEN_INVALID,
            self::TOKEN_MISSING,
            self::SESSION_EXPIRED => 401,

            // 403 Forbidden
            self::FORBIDDEN,
            self::INSUFFICIENT_PERMISSIONS,
            self::OPERATION_NOT_ALLOWED => 403,

            // 404 Not Found
            self::RESOURCE_NOT_FOUND,
            self::FILE_NOT_FOUND => 404,

            // 405 Method Not Allowed
            self::METHOD_NOT_ALLOWED => 405,

            // 409 Conflict
            self::RESOURCE_ALREADY_EXISTS,
            self::CONFLICT => 409,

            // 410 Gone
            self::GONE,
            self::RESOURCE_DELETED => 410,

            // 413 Payload Too Large
            self::REQUEST_ENTITY_TOO_LARGE => 413,

            // 414 URI Too Long
            self::URI_TOO_LONG => 414,

            // 415 Unsupported Media Type
            self::UNSUPPORTED_MEDIA_TYPE => 415,

            // 422 Unprocessable Entity
            self::VALIDATION_FAILED,
            self::MISSING_REQUIRED_FIELD,
            self::INVALID_STATE,
            self::RESOURCE_LOCKED,
            self::PRECONDITION_FAILED => 422,

            // 429 Too Many Requests
            self::RATE_LIMIT_EXCEEDED,
            self::QUOTA_EXCEEDED,
            self::CONCURRENT_REQUEST_LIMIT => 429,

            // 501 Not Implemented
            self::NOT_IMPLEMENTED => 501,

            // 503 Service Unavailable
            self::SERVICE_UNAVAILABLE,
            self::MAINTENANCE_MODE,
            self::EXTERNAL_SERVICE_UNAVAILABLE => 503,

            // 504 Gateway Timeout
            self::EXTERNAL_SERVICE_TIMEOUT,
            self::TIMEOUT => 504,

            // 500 Internal Server Error (default for all other server errors)
            default => 500,
        };
    }

    /** Check if this is a server error (5xx) */
    public function isServerError(): bool
    {
        return $this->httpStatusCode() >= 500;
    }

    /** Check if this is a client error (4xx) */
    public function isClientError(): bool
    {
        $code = $this->httpStatusCode();

        return $code >= 400 && $code < 500;
    }

    /** Helper method to get the value (for backward compatibility) */
    public function getValue(): string
    {
        return $this->value;
    }
}
