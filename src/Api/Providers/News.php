<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Traits\HasFeed;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class News extends Provider
{
    use HasFeed;

    /**
     * @var array
     */
    protected $loginRequiredFor = ['last', 'all'];

    /**
     * Get user's latest news array.
     * @deprecated 4.11.0
     *
     * @param $limit
     * @return mixed
     */
    public function last($limit = 0)
    {
        return $this->all($limit);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function all($limit = 0)
    {
        $data = ['allow_stale' => true];

        return $this->getFeed($data, UrlBuilder::RESOURCE_GET_LATEST_NEWS, $limit);
    }
}
