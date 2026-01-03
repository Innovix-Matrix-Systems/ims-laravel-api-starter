# Extra Information

Additional development tools and commands.

## 🛠️ Code Generators

### CRUD Generator
```bash
php artisan make:crud Product
```
**Creates:**
- Model & Factory
- Migration
- Controller
- Service & Repository
- DTOs
- Routes
- Tests

### DTO Generator
```bash
php artisan make:dto ProductDTO
```
**Creates:**
- Data Transfer Object
- Readonly properties
- Validation rules

### Service Generator
```bash
php artisan make:service Product/ProductService
```
**Creates:**
- Service class
- Interface
- Repository binding

## 🔧 Development Commands

### Code Quality
```bash
# Fix code style
php artisan csfixer:run

# Clear all caches
php artisan optimize:clear

# Generate IDE helpers
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

### Database
```bash
# Fresh migration with seeds
php artisan migrate:fresh --seed

# Rollback specific migration
php artisan migrate:rollback --step=1

# Generate seeder
php artisan make:seeder ProductSeeder
```

### Cache Management
```bash
# Clear all caches
php artisan optimize:clear

# Clear specific cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## � Package Information

### Key Dependencies
```json
{
    "laravel/framework": "^10.0",
    "laravel/sanctum": "^3.2",
    "spatie/laravel-permission": "^5.10",
    "laravel/telescope": "^4.14",
    "laravel/pulse": "^1.0",
    "spatie/laravel-health": "^1.22",
    "spatie/laravel-backup": "^8.1",
    "maatwebsite/excel": "^3.1",
    "spatie/laravel-media-library": "^10.0"
}
```

### Development Tools
```json
{
    "laravel/pint": "^1.0",
    "laravel/sail": "^1.18",
    "barryvdh/laravel-ide-helper": "^2.13",
    "knuckleswtf/scribe": "^4.23"
}
```

## 🚀 Git Hooks

### Husky Integration
```bash
# Install hooks
npx husky install

# Add pre-commit hook
npx husky add .husky/pre-commit "php artisan csfixer:run"
```

### Pre-commit Checks
- Code style fixing
- Test execution
- Static analysis

## 🐛 Debugging

### Enable Debug Mode
```env
APP_DEBUG=true
APP_ENV=local
```

### Telescope Debugging
```bash
# Install telescope
php artisan telescope:install

# Clear telescope data
php artisan telescope:prune
```

### Log Levels
```php
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
```

---

*For detailed documentation, see the [Features Guide](features.md).*