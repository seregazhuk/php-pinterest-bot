<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use Iterator;
use seregazhuk\PinterestBot\Helpers\Pagination;

trait HasFollowers
{
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
}
