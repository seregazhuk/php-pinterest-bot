<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

trait ProviderTrait
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
