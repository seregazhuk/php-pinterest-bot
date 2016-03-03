<?php

namespace seregazhuk\PinterestBot\Contracts;

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
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * @return ResponseInterface
     */
    public function getResponse();
}
