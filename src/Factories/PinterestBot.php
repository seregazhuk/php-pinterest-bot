<?php

namespace seregazhuk\PinterestBot\Factories;

use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

class PinterestBot
{
    /**
     * Initializes Bot instance and all
     * its dependencies.
     *
     * @param string $userAgent
     *
     * @return Bot
     */
    public static function create($userAgent = "")
    {
        $request = new Request(self::getHttpClient($userAgent));

        $providersContainer = new ProvidersContainer($request);

        return new Bot($providersContainer);
    }

    /**
     * @param string $userAgent
     * @return HttpClient
     */
    protected static function getHttpClient($userAgent = "")
    {
        $httpClient = new CurlHttpClient();

        if(!empty($userAgent)) $httpClient->setUserAgent($userAgent);

        return $httpClient;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
