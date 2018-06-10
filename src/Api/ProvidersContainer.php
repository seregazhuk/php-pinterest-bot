<?php

namespace seregazhuk\PinterestBot\Api;

use SebastianBergmann\CodeCoverage\Report\PHP;
use seregazhuk\PinterestBot\Api\Providers\BoardSections;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\Suggestions;
use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Api\Providers\Inbox;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Topics;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Keywords;
use seregazhuk\PinterestBot\Api\Providers\Comments;
use seregazhuk\PinterestBot\Api\Providers\Password;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Exceptions\WrongProvider;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\Providers\Core\ProviderWrapper;

/**
 * @property-read Pins $pins
 * @property-read Inbox $inbox
 * @property-read User $user
 * @property-read Boards $boards
 * @property-read Pinners $pinners
 * @property-read Keywords $keywords
 * @property-read Interests $interests
 * @property-read Topics $topics
 * @property-read Auth $auth
 * @property-read Comments $comments
 * @property-read Password $password
 * @property-read Suggestions $suggestions
 * @property-read BoardSections $boardSections
 *
 * Class ProvidersContainer
 * @package seregazhuk\PinterestBot\Api
 */
class ProvidersContainer
{
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
     * Magic method to access different providers from the container.
     *
     * @param string $provider
     * @return Provider
     * @throws WrongProvider
     */
    public function __get($provider)
    {
        return $this->getProvider($provider);
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
     * @throws WrongProvider
     */
    protected function addProvider($provider)
    {
        $className = $this->resolveProviderClass($provider);

        $this->providers[$provider] = $this->buildProvider($className);
    }

    /**
     * Build Provider object.
     *
     * @param string $className
     * @return ProviderWrapper
     */
    protected function buildProvider($className)
    {
        $provider = new $className($this);

        return new ProviderWrapper($provider);
    }

    /**
     * Proxies call to Response object and returns message from
     * the error object.
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->response->getLastErrorText();
    }

    /**
     * Returns client context from Pinterest response. By default info returns from the last
     * Pinterest response. If there was no response before or the argument $reload is
     * true, we make a dummy request to the main page to update client context.
     *
     * @param bool $reload
     * @return array|null
     * @throws WrongProvider
     */
    public function getClientInfo($reload = false)
    {
        $clientInfo = $this->response->getClientInfo();

        if ($clientInfo === null || $reload) {
            /** @var User $userProvider */
            $userProvider = $this->getProvider('user');
            $userProvider->visitPage();
        }

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

    /**
     * @param string $provider
     * @return string
     * @throws WrongProvider
     */
    protected function resolveProviderClass($provider)
    {
        $className = __NAMESPACE__ . '\\Providers\\' . ucfirst($provider);

        if (!$this->checkIsProviderClass($className)) {
            throw new WrongProvider("Provider $className not found.");
        }

        return $className;
    }

    /**
     * @param string $className
     * @return bool
     */
    protected function checkIsProviderClass($className)
    {
        if (!class_exists($className)) {
            return false;
        }

        return in_array(Provider::class, class_parents($className));
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
