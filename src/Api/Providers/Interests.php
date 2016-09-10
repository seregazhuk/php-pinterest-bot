<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;

class Interests extends Provider
{
    use HasRelatedTopics;

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
     * Returns a feed of pins
     * @param string $interest
     * @param int $limit
     * @return array|bool
     */
    public function getPinsFor($interest, $limit = 0)
    {
        $params = [
            'data' => [
                'feed'             => $interest,
                'is_category_feed' => true,
            ],
            'url' => UrlBuilder::RESOURCE_GET_CATEGORY_FEED
        ];

        return $this->getPaginatedResponse(
          $params, $limit
        );
    }
}
