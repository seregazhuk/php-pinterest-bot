<?php

namespace seregazhuk\PinterestBot;

use LogicException;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Api\Providers\Conversations;
use seregazhuk\PinterestBot\Contracts\ProvidersContainerInterface;

/**
 * Class Bot
 *
 * @package Pinterest
 * @property string        $username
 * @property string        $password
 * @property Pinners       $pinners
 * @property Pins          $pins
 * @property Boards        $boards
 * @property Interests     $interests
 * @property Conversations $conversations
 */
class Bot
{
    /**
     * @var ProvidersContainerInterface
     */
    private $providersContainer;

    public function __construct(ProvidersContainerInterface $providersContainer)
    {
        $this->providersContainer = $providersContainer;

        $this->request = $providersContainer->getRequest();
        $this->response = $providersContainer->getResponse();
    }

    /**
     * Login and parsing csrfToken from cookies if success
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password)
    {
        $this->_checkCredentials($username, $password);
        $res = $this->pinners->login($username, $password);

        return $res;
    }

    /**
     * @param string $provider
     * @return Provider
     */
    public function __get($provider)
    {
        $provider = strtolower($provider);

        return $this->providersContainer->getProvider($provider);
    }

    /**
     * Validated password and login
     * @param string $username
     * @param string $password
     */
    protected function _checkCredentials($username, $password)
    {
        if (!$username || !$password) {
            throw new LogicException('You must set username and password to login.');
        }
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->providersContainer->getResponse()->getLastError();
    }
}