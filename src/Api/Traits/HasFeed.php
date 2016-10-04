<?php

namespace seregazhuk\PinterestBot\Api\Traits;

trait HasFeed
{
    /**
     * @param array $data
     * @param string $feedUrl
     * @param int $limit
     * @return \Generator
     */
    protected function getFeed($data, $feedUrl, $limit)
    {
        $params = [
            'data' => $data,
            'url'  => $feedUrl
        ];

        return $this->getPaginatedResponse($params, $limit);
    }

    /**
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return \Generator
     */
    abstract protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData');
}