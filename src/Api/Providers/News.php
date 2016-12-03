<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Traits\HasFeed;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class News extends Provider
{
    use HasFeed;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'all',
    ];

    /**
     * @param int $limit
     * @return mixed
     */
    public function all($limit = Pagination::DEFAULT_LIMIT)
    {
        $data = ['allow_stale' => true];

        return $this->getFeed($data, UrlBuilder::RESOURCE_GET_LATEST_NEWS, $limit);
    }
}
