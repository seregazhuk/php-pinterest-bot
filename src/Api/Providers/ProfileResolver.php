<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class ProfileResolver
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function profile()
    {
        $response = $this->request->exec(UrlBuilder::RESOURCE_GET_USER_SETTINGS, '');
        return Response::fromJson($response)->getResponseData();
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function extractFromProfile($key)
    {
        $profile = $this->profile();

        return isset($profile[$key]) ? $profile[$key] : null;
    }

    /**
     * Returns current user username
     *
     * @return string|null
     */
    public function username()
    {
        return $this->extractFromProfile('username');
    }

    /**
     * Returns current user id
     *
     * @return string
     */
    public function id()
    {
        return $this->extractFromProfile('id');
    }
}
