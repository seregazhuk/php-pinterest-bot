<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use Generator;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait HasRelatedTopics
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $feedUrl
 */
trait HasTopics
{
    use HandlesRequest;

    /**
     * Returns a list of related topics
     * @param string $interest
     * @return array|bool
     */
    public function getRelatedTopics($interest)
    {
        return $this->execGetRequest(
            ['interest_name' => $interest],
            UrlBuilder::RESOURCE_GET_CATEGORIES_RELATED
        );
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
        $params = [
            'data' => $this->getFeedRequestData($interest),
            'url'  => $this->getFeedUrl(),
        ];

        return $this->getPaginatedResponse($params, $limit);
    }

    /**
     * @param $interest
     * @return array
     */
    abstract protected function getFeedRequestData($interest);

    /**
     * @return string
     */
    protected function getFeedUrl()
    {
        return property_exists($this, 'feedUrl') ? $this->feedUrl : '';
    }
}