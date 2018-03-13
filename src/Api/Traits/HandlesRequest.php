<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

trait HandlesRequest
{
    /**
     * Executes a POST request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     *
     * @return Response
     */
    abstract protected function post($resourceUrl, array $requestOptions = []);

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     *
     * @param array $bookmarks
     * @return Response
     */
    abstract protected function get($resourceUrl = '', array $requestOptions = [], array $bookmarks = []);

    /**
     * @return Request
     */
    abstract public function getRequest();
}
