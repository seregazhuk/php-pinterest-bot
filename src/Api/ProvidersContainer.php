<?php

namespace seregazhuk\PinterestBot\Api;

use ReflectionClass;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;
use seregazhuk\PinterestBot\Exceptions\WrongProviderException;
use seregazhuk\PinterestBot\Contracts\ProvidersContainerInterface;

class ProvidersContainer implements ProvidersContainerInterface
{
    /**
     * References to the request and response classes that travels
     * through the application
     *
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ResponseInterface
     */
    protected $response;

    const PROVIDERS_NAMESPACE = "seregazhuk\\PinterestBot\\Api\\Providers\\";

    /**
     * A array containing the cached providers
     *
     * @var array
     */
    private $providers = [];

    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param string $provider
     * @return Provider
     * @throws WrongProviderException
     */
    public function getProvider($provider)
    {
        $provider = strtolower($provider);

        // Check if an instance has already been initiated
        if ( ! isset($this->providers[$provider])) {
            $this->addProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * @param string $provider
     * @throws WrongProviderException
     */
    private function addProvider($provider)
    {
        $className = self::PROVIDERS_NAMESPACE.ucfirst($provider);

        if ( ! class_exists($className)) {
            throw new WrongProviderException("Provider $className not found.");
        }

        $this->providers[$provider] = $this->buildProvider($className);
    }

    /**
     * Build Provider object with reflection API
     *
     * @param string $className
     * @return object
     * @throws WrongProviderException
     */
    private function buildProvider($className)
    {
        $ref = new ReflectionClass($className);
        if ( ! $ref->isInstantiable()) {
            throw new WrongProviderException('Provider class is not instantiable.');
        }

        return $ref->newInstanceArgs([$this->request, $this->response]);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
}