<?php

namespace seregazhuk\PinterestBot;

use LogicException;
use ReflectionClass;
use seregazhuk\PinterestBot\Api\CurlDecorator;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Api\Providers\Conversations;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

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
    protected $loggedIn = false;

    /**
     * A array containing the cached providers
     *
     * @var array
     */
    private $providers = [];

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

    const PROVIDERS_NAMESPACE = "seregazhuk\\PinterestBot\\Api\\Providers\\";
    const MAX_PAGINATED_ITEMS = 100;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->request = new Request(new CurlDecorator());
        $this->response = new Response();
    }

    /**
     * Login and parsing csrfToken from cookies if success
     */
    public function login()
    {
        $this->_check_credentials();
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

        // Check if an instance has already been initiated
        if ( ! isset($this->providers[$provider])) {
            $this->_addProvider($provider);
        }

        return $this->providers[$provider];
    }

    /**
     * @param string $provider
     * @throws InvalidRequestException
     */
    protected function _addProvider($provider)
    {
        $class = self::PROVIDERS_NAMESPACE.ucfirst($provider);

        if ( ! class_exists($class)) {
            throw new InvalidRequestException;
        }

        // Create a reflection of the called class
        $ref = new ReflectionClass($class);
        $obj = $ref->newInstanceArgs([$this->request, $this->response]);

        $this->providers[$provider] = $obj;
    }

    /**
     * @throws LogicException
     */
    protected function _check_credentials()
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