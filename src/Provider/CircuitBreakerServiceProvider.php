<?php

namespace acesseseucondominio\LaravelCircuitBreaker\Provider;

use acesseseucondominio\LaravelCircuitBreaker\Store\CacheCircuitBreakerStore;
use acesseseucondominio\LaravelCircuitBreaker\Store\CircuitBreakerStoreInterface;
use Illuminate\Support\ServiceProvider;

class CircuitBreakerServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../Config/circuit_breaker.php' => config_path('circuit_breaker.php'),
        ]);
    }

    public function register()
    {
        $this->app->bind(
            CircuitBreakerStoreInterface::class,
            CacheCircuitBreakerStore::class
        );
    }
}
