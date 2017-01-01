<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class HasFeed
 * @package seregazhuk\PinterestBot\Api\Traits
 */
trait HasFeed
{
    use HandlesRequest;

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
                return $this->execGetRequest($data, $feedUrl, $bookmarks);
            });
    }
}