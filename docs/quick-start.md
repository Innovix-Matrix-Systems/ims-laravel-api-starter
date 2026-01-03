# Quick Start Guide

Get started with IMS Laravel API Starter in minutes.

## Prerequisites

- PHP 8.2+
- MySQL 8.0+ or PostgreSQL 12+
- Node.js 16+
- Composer

## Standard Setup

### 1. Clone and Configure

```bash
git clone git@github.com:Innovix-Matrix-Systems/ims-laravel-api-starter.git
cd ims-laravel-api-starter
cp .env.example .env
```

### 2. Install Dependencies

```bash
composer install
npm install
npx husky install
```

### 3. Database Setup

```bash
php artisan key:generate
php artisan migrate --seed
```

### 4. Start Development

```bash
php artisan serve
```

Visit: http://localhost:8000

## Default Credentials

**Super Admin**: superadmin@ims.com / 123456

## Quick Test

```bash
# Test API
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "superadmin@ims.com", "password": "123456"}'

# Test Health
curl http://localhost:8000/api/health

# Test Documentation
open http://localhost:8000/docs
```

## Next Steps

1. **API Documentation**: Visit `/docs` for interactive API docs
2. **Observability**: Check `/observability` for monitoring tools
3. **Code Generation**: Use `php artisan make:crud Product` to generate CRUD
4. **Testing**: Run `php artisan test` to execute test suite

---

*For detailed documentation, see the [Features Guide](features.md).*