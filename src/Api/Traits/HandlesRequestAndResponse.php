<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

/**
 * Trait HandlesRequestAndResponse
 * @package seregazhuk\PinterestBot\Api\Traits
 */
trait HandlesRequestAndResponse
{
    /**
     * @return Request
     */
    abstract public function getRequest();

    /**
     * @return Response
     */
    abstract public function getResponse();

    /**
     * Executes a POST request to Pinterest API.
     *
     * @param array $requestOptions
     * @param string $resourceUrl
     * @param bool $returnResponse
     *
     * @return Response
     */
    abstract protected function execPostRequest($requestOptions, $resourceUrl, $returnResponse = false);
}