<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class News extends Provider
{
    /**
     * @var array
     */
    protected $loginRequiredFor = ['last'];

    /**
     * Get user's latest news array.
     *
     * @return array|bool
     */
    public function last()
    {
        return $this->execGetRequest(['allow_state' => true], UrlBuilder::RESOURCE_GET_LATEST_NEWS);
    }
}
