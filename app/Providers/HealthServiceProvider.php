<?php

namespace App\Providers;

use App\Checks\DatabaseMigrationCheck;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    /** Register services. */
    public function register(): void
    {
        $checks = [
            EnvironmentCheck::new(),
            DebugModeCheck::new(),
            OptimizedAppCheck::new(),
            CacheCheck::new(),
            DatabaseCheck::new(),
            DatabaseConnectionCountCheck::new()
                ->warnWhenMoreConnectionsThan(50)
                ->failWhenMoreConnectionsThan(100),
            DatabaseMigrationCheck::new(),
            // RedisCheck::new(),
            // RedisMemoryUsageCheck::new()
            //     ->warnWhenAboveMb(900)
            //     ->failWhenAboveMb(1000),
            QueueCheck::new(),
            ScheduleCheck::new(),
            UsedDiskSpaceCheck::new(),

        ];

        Health::checks($checks);
    }

    /** Bootstrap services. */
    public function boot(): void
    {
        //
    }
}
