<?php

namespace seregazhuk\PinterestBot\Helpers\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

trait ProviderHelper
{
    /**
     * @return Request
     */
    abstract protected function getRequest();

    /**
     * @return Response
     */
    abstract protected function getResponse();
}