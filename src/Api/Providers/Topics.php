<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;
use seregazhuk\PinterestBot\Api\Providers\Core\FollowableProvider;

class Topics extends FollowableProvider
{
    use HasRelatedTopics;

    protected $followUrl   = UrlBuilder::RESOURCE_FOLLOW_INTEREST;
    protected $unFollowUrl = UrlBuilder::RESOURCE_UNFOLLOW_INTEREST;

    protected $entityIdName = 'interest_id';

    /**
     * Get category info
     *
     * @param string $topic
     * @return array|bool
     */
    public function info($topic)
    {
        return $this->get(UrlBuilder::RESOURCE_GET_TOPIC, ['interest' => $topic]);
    }

    /**
     * Returns a feed of pins.
     *
     * @param string $interest
     * @param int $limit
     * @return Pagination
     */
    public function pins($interest, $limit = Pagination::DEFAULT_LIMIT)
    {
        $data = [
            'interest'  => $interest,
            'pins_only' => false,
        ];

        return $this->paginate(UrlBuilder::RESOURCE_GET_TOPIC_FEED, $data, $limit);
    }

    /**
     * Returns an array of trending topics from http://pinterest.com/discover page. Then
     * you can use an id of each topic to get trending pins for this topic with
     * $bot->pins->explore() method.
     *
     * @return array
     */
    public function explore()
    {
        $data = [
            'aux_fields' => [],
            'offset'     => 180,
        ];

        return $this->get(UrlBuilder::RESOURCE_EXPLORE_SECTIONS, $data);
    }
}
