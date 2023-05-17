<?php

namespace acesseseucondominio\LaravelCircuitBreaker\Facade;

use acesseseucondominio\LaravelCircuitBreaker\Manager\CircuitBreakerManager;
use Illuminate\Support\Facades\Facade;

class CircuitBreaker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CircuitBreakerManager::class;
    }
}
