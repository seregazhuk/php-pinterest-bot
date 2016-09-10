<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;

class Topics extends Provider
{
    use Followable, HasRelatedTopics;

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
     * Returns a feed of pins
     * @param string $topic
     * @param int $limit
     * @return array|bool
     */
    public function getPinsFor($topic, $limit = 0)
    {
        $params = [
            'data' => [
                'interest'  => $topic,
                'pins_only' => false,
            ],
            'url' => UrlBuilder::RESOURCE_GET_TOPIC_FEED
        ];

        return $this->getPaginatedResponse(
            $params, $limit
        );
    }
}