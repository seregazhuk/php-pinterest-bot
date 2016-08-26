<?php

namespace seregazhuk\PinterestBot\Factories;

use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

class PinterestBot
{
    /**
     * Initializes Bot instance and all
     * its dependencies.
     *
     * @param string $userAgent
     * @param array $curlOpts
     *
     * @return Bot
     */
    public static function create($userAgent = "", $curlOpts = [])
    {
        $request = new Request(new CurlHttpClient(), $curlOpts);
        if (!empty($userAgent)) {
            $request->setUserAgent($userAgent);
        }

        $providersContainer = new ProvidersContainer($request);

        return new Bot($providersContainer);
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
