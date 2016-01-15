<?php

namespace seregazhuk\PinterestBot\Api;

use ReflectionClass;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Contracts\ProvidersContainerInterface;
use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;
use seregazhuk\PinterestBot\Exceptions\WrongProviderException;

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
        $class = self::PROVIDERS_NAMESPACE.ucfirst($provider);

        if ( ! class_exists($class)) {
            throw new WrongProviderException;
        }

        // Create a reflection of the called class
        $ref = new ReflectionClass($class);
        $obj = $ref->newInstanceArgs([$this->request, $this->response]);

        $this->providers[$provider] = $obj;
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