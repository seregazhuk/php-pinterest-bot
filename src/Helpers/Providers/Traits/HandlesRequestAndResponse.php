<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use seregazhuk\PinterestBot\Contracts\RequestInterface;
use seregazhuk\PinterestBot\Contracts\ResponseInterface;

trait HandlesRequestAndResponse
{
    /**
     * @return RequestInterface
     */
    abstract public function getRequest();

    /**
     * @return ResponseInterface
     */
    abstract public function getResponse();
}