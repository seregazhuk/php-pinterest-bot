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
     * @param bool $returnData
     *
     * @return bool|array
     */
    abstract protected function post($resourceUrl, array $requestOptions = [], $returnData = false);

    /**
     * Executes a GET request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param array $requestOptions
     *
     * @return bool|array
     */
    abstract protected function get($resourceUrl = '', array $requestOptions = []);

    /**
     * Low level request method, which simply returns html for the specified url.
     *
     * @param string $url
     * @return string
     */
    abstract protected function getHtml($url);

    /**
     * @return Response
     */
    abstract public function getResponse();

    /**
     * @return Request
     */
    abstract public function getRequest();
}
