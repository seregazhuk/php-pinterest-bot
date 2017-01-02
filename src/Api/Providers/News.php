<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class News extends Provider
{
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

        return $this->paginate($data, UrlBuilder::RESOURCE_GET_LATEST_NEWS, $limit);
    }
}
