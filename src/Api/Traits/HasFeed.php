<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\Pagination;

trait HasFeed
{
    /**
     * @param array $data
     * @param string $feedUrl
     * @param int $limit
     * @return Pagination
     */
    protected function getFeed($data, $feedUrl, $limit)
    {
        return (new Pagination($limit))
            ->paginateOver(function($bookmarks = []) use ($data, $feedUrl) {
                return $this->getPaginatedData($data, $feedUrl, $bookmarks);
            });
    }
}