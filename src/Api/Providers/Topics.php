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
    protected $feedUrl = UrlBuilder::RESOURCE_GET_TOPIC_FEED;


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
     * @param $interest
     * @return array
     */
    protected function getFeedRequestData($interest)
    {
        return [
            'interest'  => $interest,
            'pins_only' => false,
        ];
    }
}