# App Health Check

Application health monitoring endpoint powered by Spatie Health, providing real-time system status and diagnostics.

## 🏥 Health Check Endpoint

**URL**: `http://localhost:8000/api/health`
**Method**: `GET`
**Format**: JSON

## 🔍 Available Checks

### System Checks
- **Environment**: Verifies if the application is running in the expected environment (e.g., `production`).
- **Debug Mode**: Checks if `APP_DEBUG` is enabled (should be `false` in production).
- **Used Disk Space**: Monitors disk usage percentage (Warnings at high usage).

### Application Checks
- **Optimized App**: Checks if configuration, routes, and views are cached for performance.
- **Cache**: Verifies that the cache driver is functioning correctly.
- **Database Migration** (Custom): Checks if there are any pending database migrations.
  - **Status**: `ok` if up to date, `warning` if migrations are pending.

### Service Checks
- **Database**: Verifies connection to the primary database.
- **Database Connection Count**: Monitors open database connections.
  - **Warn**: > 50 connections
  - **Fail**: > 100 connections
- **Queue**: Checks if the queue worker is running and processing jobs.
- **Schedule**: Verifies that the Laravel scheduler is running as expected.

## 📄 Response Structure

The endpoint returns a JSON object containing the overall status and detailed results for each check.

### Response Example

```json
{
  "finishedAt": 1715624890,
  "checkResults": [
    {
      "name": "Environment",
      "label": "Environment",
      "notificationMessage": "The environment was expected to be `production`, but actually was `local`",
      "shortSummary": "local",
      "status": "failed",
      "meta": {
        "actual": "local",
        "expected": "production"
      }
    },
    {
      "name": "DebugMode",
      "label": "Debug Mode",
      "notificationMessage": "The debug mode was expected to be `false`, but actually was `true`",
      "shortSummary": "true",
      "status": "failed",
      "meta": {
        "actual": true,
        "expected": false
      }
    },
    {
      "name": "OptimizedApp",
      "label": "Optimized App",
      "notificationMessage": "Configs are not cached.",
      "shortSummary": "Failed",
      "status": "failed",
      "meta": []
    },
    {
      "name": "Cache",
      "label": "Cache",
      "notificationMessage": "",
      "shortSummary": "Ok",
      "status": "ok",
      "meta": {
        "driver": "file"
      }
    },
    {
      "name": "Database",
      "label": "Database",
      "notificationMessage": "",
      "shortSummary": "Ok",
      "status": "ok",
      "meta": {
        "connection_name": "mysql"
      }
    },
    {
      "name": "DatabaseConnectionCount",
      "label": "Database Connection Count",
      "notificationMessage": "",
      "shortSummary": "2 connections",
      "status": "ok",
      "meta": {
        "connection_count": 2
      }
    },
    {
      "name": "DatabaseMigration",
      "label": "Database Migration",
      "notificationMessage": "",
      "shortSummary": "All migrations are up to date.",
      "status": "ok",
      "meta": []
    },
    {
      "name": "Queue",
      "label": "Queue",
      "notificationMessage": "Queue jobs running failed. Check meta for more information.",
      "shortSummary": "Failed",
      "status": "failed",
      "meta": [
        "The last run of the `default` queue was more than 10 minutes ago."
      ]
    },
    {
      "name": "Schedule",
      "label": "Schedule",
      "notificationMessage": "The last run of the schedule was more than 5 minutes ago.",
      "shortSummary": "Failed",
      "status": "failed",
      "meta": []
    },
    {
      "name": "UsedDiskSpace",
      "label": "Used Disk Space",
      "notificationMessage": "",
      "shortSummary": "45%",
      "status": "ok",
      "meta": {
        "disk_space_used_percentage": 45
      }
    }
  ]
}
```

## 🚨 Troubleshooting

### Common Failures & Fixes

**1. Environment / Debug Mode Failed**
```bash
# Set production environment in .env
APP_ENV=production
APP_DEBUG=false

# Clear config cache
php artisan config:clear
```

**2. Optimized App Failed**
```bash
# Cache configurations for production
php artisan optimize
# Or individually:
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**3. Database Migration Warning**
```bash
# Run pending migrations
php artisan migrate --force
```

**4. Queue / Schedule Failed**
Ensure your supervisor or worker process is running:
```bash
# Run queue worker manually
php artisan queue:work

# Run scheduler manually
php artisan schedule:work
```

---

*For detailed documentation, see the [Features Guide](features.md).*
