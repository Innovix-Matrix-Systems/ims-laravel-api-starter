# API Documentation Guide

Interactive API documentation with multiple interfaces.

## 📚 Documentation Interfaces

### Scalar UI
- **URL**: http://localhost:8000/docs
- **Features**: Modern interface, interactive testing, dark/light themes

### Swagger UI
- **URL**: http://localhost:8000/docs/swagger
- **Features**: Classic interface, try-it-out functionality

### OpenAPI Spec
- **YAML**: http://localhost:8000/docs/openapi.yaml
- **JSON**: http://localhost:8000/docs/openapi.json

### Postman Collection
- **URL**: http://localhost:8000/docs/collection.json

## 🔐 Authentication

Most endpoints require Bearer token authentication:

1. **Login**: `POST /api/v1/login`
2. **Copy token** from response
3. **Authorize** in documentation interface
4. **Test** protected endpoints

**Header Format:**
```
Authorization: Bearer your-token-here
```

## 🎯 Key Endpoints

### Health Check
```bash
GET /api/health
```

### Authentication
```bash
POST /api/v1/login
POST /api/v1/verify-otp
POST /api/v1/logout
```

### User Management
```bash
GET    /api/v1/user
GET    /api/v1/user/profile
GET    /api/v1/user/{id}
POST   /api/v1/user
PATCH  /api/v1/user/{id}
DELETE /api/v1/user/{id}
POST   /api/v1/user/assign-role
POST   /api/v1/user/change-password
POST   /api/v1/user/export
POST   /api/v1/user/profile/update
POST   /api/v1/user/profile/change-password
POST   /api/v1/user/profile/update-avatar
```

### Role Management
```bash
GET    /api/v1/role
GET    /api/v1/role/{id}
POST   /api/v1/role
PATCH  /api/v1/role/{id}
DELETE /api/v1/role/{id}
POST   /api/v1/role/assign-permission
```

### Permission Management
```bash
GET    /api/v1/permission
POST   /api/v1/permission
DELETE /api/v1/permission/{id}
GET    /api/v1/permission/user
```

## 🚀 Quick Test

```bash
# Login
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "superadmin@ims.com", "password": "123456"}'

# Get users (with token)
curl -X GET http://localhost:8000/api/v1/user \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🔧 Configuration

API documentation is configured in `config/scribe.php` with custom generators:

```php
'strategies' => [
    'bodyParameters' => [
        ...Defaults::BODY_PARAMETERS_STRATEGIES,
        \App\Http\Documentation\Strategies\OptionalFileUploadDetector::class,
    ],
    'responses' => configureStrategy(
        [
            ...Defaults::RESPONSES_STRATEGIES,
            \App\Http\Documentation\Strategies\ParseAdditionalAnnotation::class,
        ]
    ),
],
```

## 🛠️ Custom Generators

### ImsPreferredAdditionalMetaDataGenerator
- **Purpose**: Adds custom metadata to API responses
- **Features**: Merges `@additional` annotation data into response examples
- **Usage**: Add `@additional {"meta": {"custom_field": "value"}}` to controller methods

### ImsPreferredErrorResponseGenerator
- **Purpose**: Automatically generates standard error responses
- **Features**: Adds 401, 403, 422, and 500 error responses with proper schemas
- **Usage**: Automatically applied to all endpoints

### ImsPreferredOpenApiGenerator
- **Purpose**: Enhances OpenAPI specification generation
- **Features**: 
  - Adds required headers (Accept, Content-Type)
  - Handles optional file uploads (supports both JSON and multipart)
  - Generates proper examples for different content types
- **Usage**: Automatically applied to all endpoints

### OptionalFileUploadDetector
- **Purpose**: Detects endpoints with optional file uploads
- **Features**: Identifies FormRequest classes with optional file fields
- **Usage**: Automatically detects and configures dual content-type support

### ParseAdditionalAnnotation
- **Purpose**: Extracts custom `@additional` annotations
- **Features**: Parses JSON data from controller docblocks
- **Usage**: Add `@additional {"key": "value"}` to controller methods

## 🔄 Regenerate Documentation

```bash
php artisan scribe:generate
```

## 📋 Example with Custom Annotations

```php
/**
 * Update user profile
 * 
 * @additional {
 *   "meta": {
 *     "timestamp": "2024-01-01T00:00:00Z",
 *     "version": "1.0"
 *   },
 *   "custom_field": "value"
 * }
 */
public function updateProfile(UpdateProfileRequest $request)
{
    // Your implementation
}
```

---



*For detailed documentation, see the [Features Guide](features.md).*