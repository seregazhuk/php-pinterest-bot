<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

class ProviderLoginCheckWrapper
{
    /**
     * @var Provider
     */
    private $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Run login check before every method if needed.
     *
     * @param $method
     * @param $arguments
     *
     * @throws AuthException
     * @throws InvalidRequestException
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
        throw new InvalidRequestException($errorMessage);
    }

    /**
     * Checks if method requires login and if true,
     * checks logged in status.
     *
     * @param $method
     *
     * @throws AuthException if is not logged in
     */
    protected function checkMethodForLoginRequired($method)
    {
        $isLoggedIn = $this->provider->getRequest()->isLoggedIn();
        $methodRequiresLogin = $this->provider->checkMethodRequiresLogin($method);

        if ($methodRequiresLogin && !$isLoggedIn) {
            $errorMessage = $this->getErrorMethodCallMessage($method, "You must log in before.");
            throw new AuthException($errorMessage);
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
