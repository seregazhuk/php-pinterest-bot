<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class News extends Provider
{
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
    public function last($limit)
    {
        return $this->all($limit);
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function all($limit = 0)
    {
        $params = [
            'data' => ['allow_stale' => true],
            'url'  => UrlBuilder::RESOURCE_GET_LATEST_NEWS
        ];

        return $this->getPaginatedResponse($params, $limit);
    }
}
