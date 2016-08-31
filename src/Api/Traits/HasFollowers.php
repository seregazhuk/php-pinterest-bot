<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use Iterator;

/**
 * Trait HasFollowers
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $followersUrl
 * @property string $followersFor
 */
trait HasFollowers
{
    /**
     * Get followers.
     *
     * @param string $for
     * @param int $limit
     *
     * @return Iterator
     */
    public function followers($for, $limit = 0)
    {
        return $this->getFollowData(
            [$this->getFollowersFor() => $for], $this->getFollowersUrl(), $limit
        );
    }
    
    /**
     * @param array  $data
     * @param string $resourceUrl
     * @param int $limit
     *
     * @return Iterator
     */
    public function getFollowData($data, $resourceUrl, $limit = 0)
    {
        $requestData = array_merge([$data, $resourceUrl]);

        return $this->getPaginatedResponse($requestData, $limit);
    }

    /**
     * @return string
     */
    protected function getFollowersUrl()
    {
        return property_exists($this, 'followersUrl') ? $this->followersUrl : '';
    }

    /**
     * @return string
     */
    protected function getFollowersFor()
    {
        return property_exists($this, 'followersFor') ? $this->followersFor : '';
    }

    /**
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return mixed
     */
    abstract protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData');
}
