<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class News extends Provider
{
    protected $loginRequired = ['latest'];

    /**
     * Get user's latest news array
     * @return array
     */
    public function latest()
    {
        $this->request->checkLoggedIn();

        $data = ["options" => ['allow_state' => true]];
        $request = Request::createRequestData($data);

        $getString = UrlHelper::buildRequestString($request);
        $response = $this->request->exec(UrlHelper::RESOURCE_GET_LATEST_NEWS . "?{$getString}");

        return $this->response->getData($response);
    }
}
