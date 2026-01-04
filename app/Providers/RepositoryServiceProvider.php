<?php

namespace App\Providers;

use App\Repositories\Contracts\DataProcessingJobRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\DataProcessingJob\DataProcessingJobRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /** Register services. */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(DataProcessingJobRepositoryInterface::class, DataProcessingJobRepository::class);
    }

    /** Bootstrap services. */
    public function boot(): void
    {
        //
    }
}
