<?php

namespace seregazhuk\PinterestBot\Factories;

use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

class PinterestBot
{
    public static function create()
    {
        $request = new Request(new CurlAdapter());
        $response = new Response();
        $providersContainer = new ProvidersContainer($request, $response);

        return new Bot($request, $response, $providersContainer);
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}