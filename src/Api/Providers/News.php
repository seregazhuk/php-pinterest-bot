<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

class News extends Provider
{
    protected $loginRequired = ['latest'];

    /**
     * Get user's latest news array.
     *
     * @return array
     */
    public function last()
    {
        return $this->execGetRequest(['allow_state' => true], UrlHelper::RESOURCE_GET_LATEST_NEWS);
    }
}
