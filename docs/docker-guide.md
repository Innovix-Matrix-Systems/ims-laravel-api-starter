# Docker Guide

Production-ready Docker setup with multi-stage builds and optimized configuration.

## 🐳 Overview

This project provides a **robust Dockerfile** designed for production deployments. No docker-compose.yml is provided intentionally - you have full flexibility to integrate with your preferred orchestration solution.

## 🏗️ Multi-Stage Build

### Stage 1: Builder
- **Base**: PHP 8.2-FPM Alpine
- **Purpose**: Install dependencies and build application
- **Optimizations**: Production composer install, autoloader optimization

### Stage 2: Production
- **Base**: PHP 8.2-FPM Alpine  
- **Purpose**: Runtime environment
- **Optimizations**: OPcache, production PHP settings, health checks

## 🚀 Usage Examples

### Basic Container Run
```bash
# Build the image
docker build -t ims-laravel-app .

# Run with environment variables
docker run -d \
  --name laravel-app \
  -p 8000:8000 \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=your-db \
  -e DB_USERNAME=your-user \
  -e DB_PASSWORD=your-password \
  -e APP_KEY=your-app-key \
  ims-laravel-app
```

### With Docker Compose (User-Defined)
```yaml
version: '3.8'
services:
  app:
    build: .
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=db
      - DB_DATABASE=ims_laravel
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - db
  
  db:
    image: mysql:8.0
    environment:
      - MYSQL_ROOT_PASSWORD=secret
      - MYSQL_DATABASE=ims_laravel
```

### With Kubernetes
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: laravel-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: laravel
  template:
    spec:
      containers:
      - name: app
        image: ims-laravel-app
        ports:
        - containerPort: 8000
        env:
        - name: DB_HOST
          value: "mysql-service"
```

## 🔧 Entrypoint Scripts

### Main Application (`docker-entrypoint.sh`)
- Waits for database connection
- Runs migrations automatically
- Links storage directory
- Optimizes application
- Generates API documentation

### Queue Worker (`docker-entrypoint-queue.sh`)
- Designed for queue worker containers
- Waits for app deployment (60s initial delay)
- Exponential backoff for database connection
- Auto-restart on failure

### Scheduler (`docker-entrypoint-scheduler.sh`)
- Designed for cron job execution
- Same database waiting logic as queue worker
- Handles Laravel scheduler tasks

### Database Wait Script (`wait-for-db.sh`)
- Robust database connectivity checking
- Supports both mysql client and PHP PDO fallback
- Configurable connection parameters
- Detailed error reporting

## 🏭 Production Optimizations

### PHP Configuration
- Memory limit: 256MB
- Max execution time: 30s
- Upload limits: 32MB
- OPcache enabled with production settings
- Expose PHP disabled

### OPcache Settings
- Memory consumption: 128MB
- Max accelerated files: 4000
- Revalidate frequency: 2s
- Fast shutdown enabled
- Timestamp validation disabled

### Health Monitoring
- Built-in health check endpoint
- 60-second intervals
- 3 retry attempts
- 30-second startup period

## 🌍 Environment Configuration

### Required Environment Variables
```bash
APP_KEY=base64:your-app-key-here
DB_HOST=your-database-host
DB_DATABASE=your-database-name
DB_USERNAME=your-database-user
DB_PASSWORD=your-database-password
```

### Optional Optimizations
```bash
# Cache configuration
CACHE_DRIVER=redis
REDIS_HOST=your-redis-host

# Queue configuration
QUEUE_CONNECTION=redis

# Session configuration
SESSION_DRIVER=redis
```

## � Container Features

### Multi-Architecture Support
- Based on Alpine Linux for minimal size
- PHP 8.2-FPM optimized for production
- Multi-stage build reduces final image size

### Security Hardening
- Runs as www-data user
- Proper file permissions set
- No unnecessary packages in final image

### Built-in Tools
- Composer for dependency management
- curl for health checks
- PHP extensions: bcmath, gd, pdo_mysql, zip, pcntl, posix, opcache, exif

## 🔄 Deployment Strategies

### Blue-Green Deployment
```bash
# Build new version
docker build -t ims-laravel-app:v2 .

# Run health checks
docker run --rm ims-laravel-app:v2 php artisan health:check

# Switch traffic
# Update load balancer to point to new containers
```

### Rolling Updates
```bash
# Update deployment with new image
kubectl set image deployment/laravel-app app=ims-laravel-app:v2

# Monitor rollout
kubectl rollout status deployment/laravel-app
```

### Zero-Downtime Deployment
```bash
# Use health checks and graceful shutdown
# Container handles SIGTERM gracefully
# Database migrations run automatically on startup
```

---

*For detailed documentation, see the [Features Guide](features.md).*