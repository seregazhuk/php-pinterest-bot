<?php

namespace seregazhuk\PinterestBot\Api;

use ReflectionClass;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Exceptions\WrongProvider;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Providers\ProviderWrapper;

class ProvidersContainer
{
    const PROVIDERS_NAMESPACE = 'seregazhuk\\PinterestBot\\Api\\Providers\\';

    /**
     * References to the request that travels
     * through the application.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * A array containing the cached providers.
     *
     * @var array
     */
    protected $providers = [];

    /**
     * @param Request $request
     * @param Response $response
     */
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
     * @throws WrongProvider
     *
     * @return Provider
     */
    public function getProvider($provider)
    {
        $provider = strtolower($provider);

        // Check if an instance has already been initiated. If not
        // build it and then add to the providers array.
        if (!isset($this->providers[$provider])) {
            $this->addProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * Creates provider by class name, and if success saves
     * it to providers array. Provider class must exist in PROVIDERS_NAMESPACE.
     *
     * @param string $provider
     *
     * @throws WrongProvider
     */
    protected function addProvider($provider)
    {
        $className = self::PROVIDERS_NAMESPACE.ucfirst($provider);

        if (!class_exists($className)) {
            throw new WrongProvider("Provider $className not found.");
        }

        $this->providers[$provider] = $this->buildProvider($className);
    }

    /**
     * Build Provider object with reflection API.
     *
     * @param string $className
     *
     * @throws WrongProvider
     *
     * @return object
     */
    protected function buildProvider($className)
    {
        $provider = (new ReflectionClass($className))
            ->newInstanceArgs([$this->request, $this->response]);

        return new ProviderWrapper($provider);
    }

    /**
     * Proxies call to Request object and returns message from
     * the error object.
     *
     * @return string|null
     */
    public function getLastError()
    {
        $error = $this
            ->response
            ->getLastError();

        return isset($error['message']) ? $error['message'] : null;
    }

    /**
     * Simply proxies call to Response object.
     *
     * @return array|null
     */
    public function getClientInfo()
    {
        return $this->response->getClientInfo();
    }

    /**
     * Returns HttpClient object for setting user-agent string or
     * other CURL available options.
     *
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->request->getHttpClient();
    }
}
