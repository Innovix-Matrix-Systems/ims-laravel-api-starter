# Backup System

Automated application and database backup with [Spatie Laravel Backup](https://github.com/spatie/laravel-backup).

## 💾 Backup Commands

### Manual Backup
```bash
php artisan backup:run
```

### List Backups
```bash
php artisan backup:list
```

### Clean Old Backups
```bash
php artisan backup:clean
```

## 🏗️ Configuration

Backup configuration in `config/backup.php`:

```php
return [
    'backup' => [
        'name' => env('APP_NAME', 'laravel-backup'),
        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                ],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
            ],
            'databases' => [
                'mysql',
            ],
        ],
        'destination' => [
            'disks' => [
                'local',
            ],
        ],
    ],
];
```

## ☁️ Cloud Storage

Configure cloud storage in `.env`:

```env
# AWS S3
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
```

## 📦 Backup Contents

### Database Backup
- Complete database dump
- Multiple database support
- Compression enabled

### File Backup
- Application files
- Storage directory
- Configuration files
- Excludes vendor/node_modules

## 🕐 Automated Backups

Schedule backups in `app/routes/console.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:clean')->daily();
    $schedule->command('backup:run')->daily();
}
```

## 🚀 Quick Start

```bash
# Create backup
php artisan backup:run

# Check backup status
php artisan backup:list

# Clean old backups
php artisan backup:clean
```

---

*For detailed documentation, see the [Features Guide](features.md).*