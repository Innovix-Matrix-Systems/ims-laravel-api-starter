# IMS Laravel API Starter

A production-ready Laravel API starter kit with enterprise-grade features. Built-in authentication, RBAC, comprehensive API documentation, advanced observability tools, and production-ready testing infrastructure. Designed for rapid development with clean architecture patterns.

<p align="center">
  <img src="image1.png" alt="Observability Dashboard" width="100%" />
  <br>
  <em>Observability Dashboard</em>
</p>

<p align="center">
  <img src="image2.png" alt="Scalar API Dashboard" width="100%" />
  <br>
  <em>Scalar API Documentation</em>
</p>

## Features

- 🔐 **Laravel Sanctum** - Secure API authentication with personal access tokens
- 📱 **Multi-Device Login** - Device-specific token management with logout capabilities
- 🔢 **OTP Authentication** - Configurable phone-based OTP login flow with rate limiting
- 🔑 **RBAC System** - Role-based access control with permissions and roles
- 📚 **API Documentation** - Scalar, Swagger UI, OpenAPI with Postman compatibility
- 📊 **Observability** - Telescope, Pulse, Health with unified dashboard
- 🛠️ **Clean Architecture** - Repository pattern, DTOs, service layer
- 💾 **Data Management** - User management, data export/import, media library, backups
- 📤 **Background Import/Export** - Queue-based bulk user data processing with Excel/CSV support
- 📊 **Job Tracking** - Real-time monitoring of background jobs with progress tracking
- 🧹 **Automated Cleanup** - Scheduled cleanup of completed jobs and temporary files
- 🌍 **Internationalization** - Multi-language support (English, Bengali), ability to add more as needed
- 🐳 **Docker Support** - Complete containerized development environment
- ⚡ **Development Tools** - Code generators, IDE helpers, Git hooks
- 🧪 **Production-Ready Testing** - Pest PHP with Mockery, comprehensive feature/unit tests, queue testing, DTO validation

## Quick Start

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Node.js 16+
- Composer

### Development Setup

1. **Clone and setup**
   ```bash
   git clone git@github.com:Innovix-Matrix-Systems/ims-laravel-api-starter.git
   cd ims-laravel-api-starter
   cp .env.example .env
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   npx husky install
   ```

3. **Database setup**
   ```bash
   php artisan migrate --seed
   ```

4. **Start development**
   ```bash
   php artisan serve
   ```

### Default Credentials
- **Super Admin**: superadmin@ims.com / 123456

**Development Environment Compatibility:** Works seamlessly with modern development tools including [Laravel Herd](https://herd.laravel.com/) (blazing fast native Laravel environment), [FlyEnv](https://www.flyenv.com/) (all-in-one full-stack environment), and [Laragon](https://laragon.org/) (lightweight Windows development environment)

## 📖 Documentation

Our comprehensive documentation covers everything from setup to advanced features. Learn about background job processing, OTP authentication configuration, API endpoints, monitoring tools, and deployment strategies. Whether you're setting up for the first time or scaling for production, our detailed guides provide step-by-step instructions and best practices.

🌐 **[GitHub Wiki](https://github.com/Innovix-Matrix-Systems/ims-laravel-api-starter/wiki)**

*The [`docs/`](docs/) folder contains a local mirror of the wiki for offline access.*

### API Documentation

- **Scalar UI**: http://localhost:8000/docs
- **Swagger UI**: http://localhost:8000/docs/swagger
- **OpenAPI Spec**: http://localhost:8000/docs/openapi.yaml

### Observability

- **Dashboard**: http://localhost:8000/observability
- **Health Check**: http://localhost:8000/health

> **Note**: Observability tools require authentication

## Commands

```bash
# Code generation
php artisan make:crud Product // All necessary skeleton files
php artisan make:dto ProductDTO
php artisan make:service Product/ProductService
php artisan make:repo Product/ProductRepository
// and many more, check wiki!

# Code quality
php artisan pint
php artisan optimize:clear
php artisan ide-helper:generate
php artisan ide-helper:models -N
// and many more, check wiki!
```

## License

This project is licensed under the `MIT License` - see the [LICENSE.md](LICENSE.md) file for details.