<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Interfaces\RequestInterface;
use seregazhuk\PinterestBot\Interfaces\ResponseInterface;
use seregazhuk\PinterestBot\Helpers\Providers\ProviderHelper;

/**
 * Class Provider
 *
 * @package seregazhuk\PinterestBot\Interfaces
 */
class Provider
{
    use ProviderHelper;
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

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    protected function getResponse()
    {
        return $this->response;
    }
}