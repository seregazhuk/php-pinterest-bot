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

    /**
     * @param array $data
     * @param string $url
     * @param $bookmarks
     * @return Response
     */
    abstract function getPaginatedData(array $data, $url, $bookmarks = []);
}