<?php

namespace seregazhuk\PinterestBot\Contracts;

use Guzzle\Http\Message\Response;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Providers\Provider;

interface ProvidersContainerInterface
{
    /**
     * @param string $provider
     *
     * @return Provider
     */
    public function getProvider($provider);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @return Response
     */
    public function getResponse();
}
