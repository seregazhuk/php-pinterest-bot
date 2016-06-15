<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use Iterator;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class HasFollowers
 * @package seregazhuk\PinterestBot\Helpers\Providers\Traits
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

        return (new Pagination($this))->paginateOver('getPaginatedData', $requestData, $limit);
    }

    protected function getFollowersUrl()
    {
        return property_exists($this, 'followersUrl') ? $this->followersUrl : '';
    }

    protected function getFollowersFor()
    {
        return property_exists($this, 'followersFor') ? $this->followersFor : '';
    }
}
