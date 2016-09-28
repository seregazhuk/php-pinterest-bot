<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Generator;
use seregazhuk\PinterestBot\Api\Traits\HasFeed;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;

class Topics extends Provider
{
    use Followable, HasRelatedTopics, HasFeed;

    /**
     * @var array
     */
    protected $loginRequiredFor = ['follow', 'unFollow'];

    protected $followUrl   = UrlBuilder::RESOURCE_FOLLOW_INTEREST;
    protected $unFollowUrl = UrlBuilder::RESOURCE_UNFOLLOW_INTEREST;

    protected $entityIdName = 'interest_id';


    /**
     * Get category info
     *
     * @param string $topic
     * @return array|bool
     */
    public function getInfo($topic)
    {
        return $this->execGetRequest(["interest" => $topic], UrlBuilder::RESOURCE_GET_TOPIC);
    }

    /**
     * Returns a feed of pins.
     *
     * @param string $interest
     * @param int $limit
     * @return Generator
     */
    public function getPinsFor($interest, $limit = 0)
    {
        $data = [
            'interest'  => $interest,
            'pins_only' => false,
        ];

        return $this->getFeed($data, UrlBuilder::RESOURCE_GET_TOPIC_FEED, $limit);
    }
}