<?php

namespace acesseseucondominio\LaravelCircuitBreaker\Manager;

use acesseseucondominio\LaravelCircuitBreaker\Events\AttemptFailed;
use acesseseucondominio\LaravelCircuitBreaker\Events\ServiceFailed;
use acesseseucondominio\LaravelCircuitBreaker\Events\ServiceRestored;
use acesseseucondominio\LaravelCircuitBreaker\Service\ServiceOptionsResolver;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use acesseseucondominio\LaravelCircuitBreaker\Store\CircuitBreakerStoreInterface;
use Illuminate\Config\Repository as Config;

class CircuitBreakerManager
{
    /** @var  CircuitBreakerStoreInterface */
    private $store;

    /** @var EventDispatcher */
    private $dispatcher;

    /** @var ServiceOptionsResolver */
    private $serviceOptionsResolver;

    /**
     * CircuitBreakerManager constructor.
     *
     * @param CircuitBreakerStoreInterface $store
     * @param EventDispatcher $dispatcher
     * @param ServiceOptionsResolver $serviceOptionsResolver
     */
    public function __construct(
        CircuitBreakerStoreInterface $store,
        EventDispatcher $dispatcher,
        ServiceOptionsResolver $serviceOptionsResolver
    ) {
        $this->store = $store;
        $this->dispatcher = $dispatcher;
        $this->serviceOptionsResolver = $serviceOptionsResolver;
    }

    public function isAvailable(string $identifier) : bool
    {
        return $this->store->isAvailable($identifier);
    }

    public function reportFailure(string $identifier) : void
    {
        $wasAvailable = $this->isAvailable($identifier);

        $options = $this->serviceOptionsResolver->getOptionsFor($identifier);

        $this->store->reportFailure(
            $identifier,
            $options->getAttemptsThreshold(),
            $options->getAttemptsTtl(),
            $options->getFailureTtl()
        );

        $this->dispatcher->dispatch(new AttemptFailed($identifier));

        if ($wasAvailable && !$this->isAvailable($identifier)) {
            $this->dispatcher->dispatch(new ServiceFailed($identifier));
        }
    }

    public function reportSuccess(string $identifier) : void
    {
        $this->store->reportSuccess($identifier);
        $this->dispatcher->dispatch(new ServiceRestored($identifier));
    }
}
