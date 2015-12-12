<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;
use seregazhuk\PinterestBot\Providers\Pinners;
use seregazhuk\PinterestBot\Providers\Pins;
use seregazhuk\PinterestBot\Providers\Boards;
use seregazhuk\PinterestBot\Providers\Interests;
use seregazhuk\PinterestBot\Providers\Conversations;

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

    const PROVIDERS_NAMESPACE = "seregazhuk\\PinterestBot\\Providers\\";

    const MAX_PAGINATED_ITEMS = 100;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;

        $this->request = new Request(new Http());
    }

    /**
     * A array containing the cached providers
     *
     * @var array
     */
    private $providers = [];

    /**
     * A reference to the request class which travels
     * through the application
     *
     * @var Request
     */
    public $request;


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
     * @return mixed
     * @throws InvalidRequestException
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
        $class = self::PROVIDERS_NAMESPACE . ucfirst($provider);

        if ( ! class_exists($class)) {
            throw new InvalidRequestException;
        }

        // Create a reflection of the called class
        $ref = new \ReflectionClass($class);
        $obj = $ref->newInstanceArgs([$this->request]);

        $this->providers[$provider] = $obj;
    }

    /**
     * @throws \LogicException
     */
    protected function _check_credentials()
    {
        if ( ! $this->username || ! $this->password) {
            throw new \LogicException('You must set username and password to login.');
        }
    }
}