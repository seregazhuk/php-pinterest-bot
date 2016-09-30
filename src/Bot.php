<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\Api\Providers\News;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Api\Providers\Topics;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\Keywords;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Providers\Conversations;

/**
 * Class Bot.
 *
 * @property Pins $pins 
 * @property News $news
 * @property User $user
 * @property Boards $boards
 * @property Pinners $pinners
 * @property Keywords $keywords
 * @property Interests $interests
 * @property Topics $topics
 * @property Conversations $conversations
 *
 * @method HttpClient getHttpClient
 * @method array|null getLastError
 */
class Bot
{
    /**
     * @var ProvidersContainer
     */
    protected $providersContainer;

    /**
     * @param ProvidersContainer $providersContainer
     */
    public function __construct(ProvidersContainer $providersContainer)
    {
        $this->providersContainer = $providersContainer;
    }

    /**
     * Magic method to access different providers from the providers container.
     *
     * @param string $provider
     * @return Provider
     */
    public function __get($provider)
    {
        return $this->providersContainer->getProvider($provider);
    }

    /**
     *  All method calls are proxied to the providers container.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func([$this->providersContainer, $method], $parameters);
    }

    /**
     * Returns client context from Pinterest response. By default info returns from the last
     * Pinterest response. If there was no response before or the argument $reload is
     * true, we make a dummy request to the main page to update client context.
     *
     * @param bool $reload
     * @return array|null
     */
    public function getClientInfo($reload = false)
    {
        $clientInfo = $this->providersContainer->getClientInfo();

        if(is_null($clientInfo) || $reload) {
            $this->user->visitMainPage();
        }

        return $this->providersContainer->getClientInfo();
    }
}
