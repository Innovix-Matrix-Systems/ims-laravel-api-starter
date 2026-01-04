# Project Structure Guide

This document provides a comprehensive overview of the IMS Laravel API Starter project structure, explaining the purpose and organization of each directory and key files.

## Directory Structure Overview

```
ims-laravel-api-starter/
├── 📁 app/                     # Application core code
├── 📁 bootstrap/               # Application bootstrapping
├── 📁 config/                  # Configuration files
├── 📁 database/                # Database migrations and seeders
├── 📁 docs/                    # Project documentation
├── 📁 public/                  # Public assets and index.php
├── 📁 resources/               # Views, lang, and frontend assets
├── 📁 routes/                  # Application routes
├── 📁 storage/                 # Logs, cache, and app files
├── 📁 tests/                   # Application tests
├── 📁 vendor/                  # Composer dependencies
├── 🐳 Dockerfile              # Production container definition
└── 📄 README.md               # Main project documentation
```

## Detailed Directory Breakdown

### 📁 app/ - Application Core Code

Contains the application-specific code organized by domain.

```
app/
├── Console/                    # Artisan commands
├── DTOs/                      # Data Transfer Objects
├── Enums/                     # Application enums
├── Exceptions/                # Custom exceptions
├── Http/
│   ├── Controllers/           # HTTP controllers
│   ├── Middleware/            # HTTP middleware
│   └── Requests/              # Form requests
├── Models/                    # Eloquent models
├── Policies/                  # Authorization policies
├── Providers/                 # Service providers
├── Repositories/              # Repository pattern implementations
├── Services/                  # Business logic services
└── Traits/                    # Reusable traits
```

**Key Files:**
- `Console/Commands/`: Custom Artisan commands (CRUD generators, DTO generators)
- `DTOs/`: Data Transfer Objects with readonly properties
- `Http/Controllers/Api/V1/`: API controllers with resource responses
- `Models/`: Eloquent models with relationships and scopes
- `Repositories/`: Repository interfaces and implementations
- `Services/`: Business logic services following single responsibility

### 📁 config/ - Configuration Files

```
config/
├── app.php                    # Application settings
├── auth.php                   # Authentication configuration
├── database.php               # Database connections
├── permission.php             # Laravel Permission settings
├── scribe.php                 # API documentation config
├── telescope.php              # Laravel Telescope config
└── health.php                 # Health check configuration
```

**Key Features:**
- Multi-database support (MySQL, PostgreSQL, SQLite)
- Laravel Sanctum API authentication
- Telescope monitoring with built-in auth
- Health check endpoints configuration
- API documentation generation settings

### 📁 database/ - Database Structure

```
database/
├── factories/                 # Model factories
├── migrations/                # Database migrations
└── seeders/                   # Database seeders
```

**Migration Categories:**
- **Core**: Users, roles, permissions, personal access tokens
- **System**: Failed jobs, password resets, migrations
- **Monitoring**: Telescope, Pulse, Health tables

### 📁 tests/ - Test Suite

```
tests/
├── Feature/                   # Feature tests
├── Unit/                     # Unit tests
├── Mock/                     # Mock data classes
└── TestCase.php             # Base test case
```

**Test Structure:**
- **Feature Tests**: API endpoint testing with Pest PHP
- **Unit Tests**: Service and repository testing
- **Mock Classes**: Reusable test data generators
- **Database Testing**: Uses RefreshDatabase trait

### 📁 routes/ - Application Routes

```
routes/
├── api.php                    # API routes (v1)
├── console.php                # Artisan commands
└── web.php                    # Web routes (health, docs)
```

**API Versioning:**
- **v1 Prefix**: All API routes under `/api/v1/`
- **Resource Routes**: RESTful resource controllers
- **Auth Routes**: Login, logout, refresh, profile
- **Admin Routes**: User, role, permission management

### 📁 storage/ - Application Storage

```
storage/
├── app/                       # Application files
│   ├── backups/              # Laravel Backup files
│   ├── media/                # Media library files
│   └── public/               # Publicly accessible files
├── framework/                 # Framework cache and sessions
├── logs/                     # Application logs
└── telescope/                # Telescope monitoring data
```

### 📁 resources/ - Frontend Resources

```
resources/
├── lang/                      # Language files
│   ├── bn/                   # Bangla translations
│   └── en/                   # English translations
└── views/                     # Blade templates
    ├── docs/                  # Documentation templates
    └── health/                # Health check templates
```

**Internationalization:**
- **Bangla Support**: Complete Bangla language pack
- **English Support**: Default English translations
- **Custom Translations**: Easy to add new languages

## Key Configuration Files

### Root Level Files

```
├── .env.example              # Environment variables template
├── composer.json             # PHP dependencies and scripts
├── package.json              # Node.js dependencies
├── phpunit.xml               # PHPUnit configuration
├── pint.json                 # Laravel Pint code style
└── Dockerfile                # Docker development setup
```

### Docker Configuration

**Development Setup:**
- **App Container**: PHP 8.2 with required extensions
- **Database**: MySQL 8.0 with health checks
- **Queue Worker**: Separate queue processing container
- **Scheduler**: Cron job scheduling container

**Services:**
- **Web Server**: Nginx with PHP-FPM
- **Database**: MySQL with optimized settings
- **Cache**: Redis for session and cache storage
- **Queue**: Redis queue driver

## Architecture Patterns

### Clean Architecture Implementation

**Repository Pattern:**
- Interface definitions in `app/Repositories/Interfaces/`
- Implementations in `app/Repositories/`
- Dependency injection via service providers

**Service Layer:**
- Business logic in `app/Services/`
- Single responsibility principle
- Repository integration

**DTO Pattern:**
- Data transfer objects in `app/DTOs/`
- Readonly properties for immutability
- Type-safe data transformation

### API Design

**RESTful Resources:**
- Resource controllers for CRUD operations
- Resource collections for data transformation
- Form request validation

**Authentication:**
- Laravel Sanctum for API authentication
- Personal access tokens
- Token-based session management

**Response Standards:**
- Consistent JSON response format
- Error handling with proper HTTP codes
- Pagination support

## Development Workflow

### Code Generation

```bash
# Generate CRUD components
php artisan make:crud Product // generate all necessary skeleton files

# Generate DTO
php artisan make:dto ProductDTO

# Generate Service
php artisan make:service Product/ProductService
```

### Code Quality

```bash
# Run code style fixer
php artisan pint

# Run tests
php artisan test

# Clear caches
php artisan optimize:clear
```

### Database Operations

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Generate migration
php artisan make:migration create_products_table
```

---

*For detailed documentation, see the [Features Guide](features.md).*