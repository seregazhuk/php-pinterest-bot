<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Exceptions\AuthFailed;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

class ProviderWrapper
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @param Provider|object $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Proxies a call to a provider with a login check
     * before every method if needed.
     *
     * @param $method
     * @param $arguments
     *
     * @throws AuthFailed
     * @throws InvalidRequest
     *
     * @return mixed|null
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this->provider, $method)) {
            $this->checkMethodForLoginRequired($method);

            return call_user_func_array([$this->provider, $method], $arguments);
        }

        $errorMessage = $this->getErrorMethodCallMessage($method, "Method $method does'n exist.");
        throw new InvalidRequest($errorMessage);
    }

    /**
     * Checks if method requires login and if true,
     * checks logged in status.
     *
     * @param $method
     *
     * @throws AuthFailed if is not logged in
     */
    protected function checkMethodForLoginRequired($method)
    {
        $isLoggedIn = $this->provider->getRequest()->isLoggedIn();
        $methodRequiresLogin = $this->provider->checkMethodRequiresLogin($method);

        if ($methodRequiresLogin && !$isLoggedIn) {
            $errorMessage = $this->getErrorMethodCallMessage($method, "You must log in before.");
            throw new AuthFailed($errorMessage);
        }
    }

    /**
     * @param string $method
     * @param string $message
     * @return string
     */
    protected function getErrorMethodCallMessage($method, $message)
    {
        $providerClass = get_class($this->provider);

        return "Error calling $providerClass::$method method. $message";
    }
}
