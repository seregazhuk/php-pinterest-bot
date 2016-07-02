<?php

namespace seregazhuk\PinterestBot\Api;

use ReflectionClass;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Exceptions\WrongProviderException;
use seregazhuk\PinterestBot\Api\Providers\ProviderLoginCheckWrapper;

class ProvidersContainer
{
    /**
     * References to the request and response classes that travels
     * through the application.
     *
     * @var Request
     */
    protected $request;
    
    /**
     * @var Response
     */
    protected $response;

    const PROVIDERS_NAMESPACE = 'seregazhuk\\PinterestBot\\Api\\Providers\\';

    /**
     * A array containing the cached providers.
     *
     * @var array
     */
    private $providers = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Gets provider object by name. If there is no such provider
     * in providers array, it will try to create it, then save
     * it, and then return.
     *
     * @param string $provider
     *
     * @throws WrongProviderException
     *
     * @return Provider
     */
    public function getProvider($provider)
    {
        $provider = strtolower($provider);

        // Check if an instance has already been initiated
        if (!isset($this->providers[$provider])) {
            $this->addProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * Creates provider by class name, and if success saves
     * it to providers array. Provider class must be in PROVIDERS_NAMESPACE.
     *
     * @param string $provider
     *
     * @throws WrongProviderException
     */
    private function addProvider($provider)
    {
        $className = self::PROVIDERS_NAMESPACE.ucfirst($provider);

        if (!class_exists($className)) {
            throw new WrongProviderException("Provider $className not found.");
        }

        $this->providers[$provider] = $this->buildProvider($className);
    }

    /**
     * Build Provider object with reflection API.
     *
     * @param string $className
     *
     * @throws WrongProviderException
     *
     * @return object
     */
    private function buildProvider($className)
    {
        $provider = (new ReflectionClass($className))->newInstanceArgs([$this->request, $this->response]);

        return new ProviderLoginCheckWrapper($provider);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
