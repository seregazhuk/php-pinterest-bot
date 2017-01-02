<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\HasRelatedTopics;

class Topics extends EntityProvider
{
    use Followable, HasRelatedTopics;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'follow',
        'unFollow',
    ];

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
        return $this->execGetRequest(["interest" => $topic], UrlBuilder::RESOURCE_GET_TOPIC);
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

        return $this->paginate($data, UrlBuilder::RESOURCE_GET_TOPIC_FEED, $limit);
    }
}