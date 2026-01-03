# Observability Guide

Monitoring and debugging tools with unified dashboard.

## 📊 Dashboard

**URL**: http://localhost:8000/observability

Central hub for all monitoring tools with built-in authentication.

## 🔍 Laravel Telescope

**Features:**
- Request monitoring with timing
- Database query analysis
- Exception tracking with stack traces
- Cache performance monitoring

**Access**: Through observability dashboard

## 📈 Laravel Pulse

**Features:**
- Real-time application metrics
- User analytics and activity
- Slow query detection
- Queue performance monitoring

**Access**: Through observability dashboard

## 🏥 Laravel Health

**URL**: http://localhost:8000/api/health

Comprehensive system health status check returning JSON response.
For detailed check descriptions and response structure, see [App Health Check](app-health-check.md).

**Key Checks:**
- System (Environment, Debug Mode, Disk Space)
- Application (Cache, Optimized App)
- Services (Database, Queue, Schedule)
- Custom (Migration Status)

## 🔐 Authentication

All observability tools require authentication via built-in middleware.

## ⚙️ Configuration

Environment variables:
```env
OBSERVABILITY_ENABLED=true
OBSERVABILITY_AUTH_ENABLED=false
```

## 🚨 Troubleshooting

**Tools not loading:**
```bash
php artisan optimize:clear
php artisan telescope:prune
```

---

*For detailed documentation, see the [Features Guide](features.md).*