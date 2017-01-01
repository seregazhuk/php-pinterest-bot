<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;

trait HandlesRequest
{
    /**
     * Executes a POST request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param bool $returnResponse
     *
     * @return Response|bool
     */
    abstract protected function execPostRequest($requestOptions, $resourceUrl, $returnResponse = false);

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param array $bookmarks
     * @return array|bool|Response
     */
    abstract protected function execGetRequest(array $requestOptions = [], $resourceUrl = '', $bookmarks = []);
}