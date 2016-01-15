<?php

namespace seregazhuk\PinterestBot\Contracts;

interface ProvidersContainerInterface
{
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