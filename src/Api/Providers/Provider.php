<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Interfaces\RequestInterface;
use seregazhuk\PinterestBot\Interfaces\ResponseInterface;

/**
 * Class Provider
 *
 * @package seregazhuk\PinterestBot\Interfaces
 */
class Provider
{
    /**
     * Instance of the API RequestInterface
     *
     * @var RequestInterface
     */
    protected $request;
    protected $response;

    /**
     * @param  RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

}