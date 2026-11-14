<?php

namespace ItmoSoft\Customers;

use Illuminate\Support\ServiceProvider;

class CustomersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/customers.php', 'customers');
        $this->app->singleton(CustomersManager::class);
        $this->app->alias(CustomersManager::class, 'customers');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/customers.php' => config_path('customers.php'),
        ], 'customers-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
