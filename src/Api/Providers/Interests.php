<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Generator;
use seregazhuk\PinterestBot\Api\Traits\HasFeed;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;

class Interests extends Provider
{
    use HasRelatedTopics, HasFeed;

    protected $feedUrl = UrlBuilder::RESOURCE_GET_CATEGORY_FEED;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'main',
    ];

    /**
     * Get list of main categories
     * 
     * @return array|bool
     */
    public function main()
    {
        return $this->execGetRequest(["category_types" => "main"], UrlBuilder::RESOURCE_GET_CATEGORIES);
    }

    /**
     * Get category info
     *
     * @param string $category
     * @return array|bool
     */
    public function info($category)
    {
        return $this->execGetRequest(["category" => $category], UrlBuilder::RESOURCE_GET_CATEGORY);
    }

    /**
     * Returns a feed of pins.
     *
     * @param string $interest
     * @param int $limit
     * @return Generator
     */
    public function pins($interest, $limit = 0)
    {
       $data = [
           'feed'             => $interest,
           'is_category_feed' => true,
       ];

        return $this->getFeed($data, UrlBuilder::RESOURCE_GET_CATEGORY_FEED, $limit);
    }
}
