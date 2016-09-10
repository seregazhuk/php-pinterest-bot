<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait HasRelatedTopics
{
    /**
     * Returns a list of related interests
     * @param string $interest
     * @return array|bool
     */
    public function getRelatedTopics($interest)
    {
        return $this->execGetRequest(
            [
                'interest_name' => $interest,
            ],
            UrlBuilder::RESOURCE_GET_CATEGORIES_RELATED
        );
    }

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @return array|bool
     */
    abstract protected function execGetRequest(array $requestOptions = [], $resourceUrl = '');
}