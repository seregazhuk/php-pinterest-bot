<?php

namespace seregazhuk\PinterestBot\Factories;

use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

class PinterestBot
{
    /**
     * Initializes Bot instance and all its dependencies.
     *
     * @return Bot
     */
    public static function create()
    {
        $request = self::makeRequest();

        $providersContainer = new ProvidersContainer($request, new Response());

        return new Bot($providersContainer);
    }

    /**
     * @return Request
     */
    protected static function makeRequest()
    {
        $httpClient = new CurlHttpClient(new Cookies());

        return new Request($httpClient);
    }

    /**
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @codeCoverageIgnore
     */
    private function __clone()
    {
    }
}
