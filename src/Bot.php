<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\Api\Providers\News;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\Keywords;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Api\Providers\Conversations;

/**
 * Class Bot.
 *
 *
 * @property Pins $pins 
 * @property News $news
 * @property User $user
 * @property Boards $boards
 * @property Pinners $pinners
 * @property Keywords $keywords
 * @property Interests $interests
 * @property Conversations $conversations
 */
class Bot
{
    /**
     * @var ProvidersContainer
     */
    private $providersContainer;

    /**
     * @param ProvidersContainer $providersContainer
     */
    public function __construct(ProvidersContainer $providersContainer)
    {
        $this->providersContainer = $providersContainer;
    }

    /**
     * Magic method to access different providers.
     *
     * @param string $provider
     *
     * @return Provider
     */
    public function __get($provider)
    {
        return $this->providersContainer->getProvider($provider);
    }

    /**
     *  Magic method to proxy calls to providers container.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func([$this->providersContainer, $method], $parameters);
    }
}
