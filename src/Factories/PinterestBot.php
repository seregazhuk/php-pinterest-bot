<?php

namespace seregazhuk\PinterestBot\Factories;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Session;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

class PinterestBot
{
    /**
     * Initializes Bot instance and all its dependencies.
     *
     * @return ProvidersContainer
     */
    public static function create()
    {
        $httpClient = new CurlHttpClient(new Cookies());
        $request = new Request($httpClient, new Session());
        return new ProvidersContainer($request);
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
