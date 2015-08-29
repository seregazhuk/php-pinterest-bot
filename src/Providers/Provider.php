<?php

namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Interfaces\RequestInterface;

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

    /**
     * @param  RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }
}