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
     *
     * @return Response|bool
     */
    abstract protected function post($requestOptions, $resourceUrl);

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @return array|bool|Response
     */
    abstract protected function get(array $requestOptions = [], $resourceUrl = '');
}
