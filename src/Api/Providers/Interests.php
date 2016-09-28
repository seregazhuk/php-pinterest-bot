<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\HasTopics;

class Interests extends Provider
{
    use HasTopics;

    protected $feedUrl = UrlBuilder::RESOURCE_GET_CATEGORY_FEED;

    /**
     * Get list of main categories
     * 
     * @return array|bool
     */
    public function getMain()
    {
        return $this->execGetRequest(["category_types" => "main"], UrlBuilder::RESOURCE_GET_CATEGORIES);
    }

    /**
     * Get category info
     *
     * @param string $category
     * @return array|bool
     */
    public function getInfo($category)
    {
        return $this->execGetRequest(["category" => $category], UrlBuilder::RESOURCE_GET_CATEGORY);
    }

    /**
     * @param $interest
     * @return array
     */
    protected function getFeedRequestData($interest)
    {
        return [
            'feed'             => $interest,
            'is_category_feed' => true,
        ];
    }
}
