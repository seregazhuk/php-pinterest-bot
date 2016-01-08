<?php

namespace seregazhuk\PinterestBot;

use LogicException;
use ReflectionClass;
use seregazhuk\PinterestBot\Api\CurlAdaptor;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Api\Providers\Conversations;

/**
 * Class PinterestBot
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
class PinterestBot
{
    protected $username;
    protected $password;

    /**
     * @var ProvidersContainer
     */
    private $providersContainer;

    /**
     * References to the request and response classes that travels
     * through the application
     *
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->request = new Request(new CurlAdaptor());
        $this->response = new Response();

        $this->providersContainer = new ProvidersContainer($this->request, $this->response);
    }

    /**
     * Login and parsing csrfToken from cookies if success
     */
    public function login()
    {
        $this->_checkCredentials();
        $res = $this->pinners->login($this->username, $this->password);

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
     * @throws LogicException
     */
    protected function _checkCredentials()
    {
        if ( ! $this->username || ! $this->password) {
            throw new LogicException('You must set username and password to login.');
        }
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->response->getLastError();
    }
}