<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Generator;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Interests extends Provider
{
    use HasRelatedTopics;

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
