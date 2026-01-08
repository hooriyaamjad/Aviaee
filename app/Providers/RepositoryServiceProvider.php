<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Domain interface
use App\Domain\Interfaces\IUserRepository;

// Concrete repository
use App\Repositories\UserRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind the interface to the concrete implementation
        $this->app->bind(
            IUserRepository::class,
            UserRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
