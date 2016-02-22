<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class News extends Provider
{
    protected $loginRequired = ['latest'];

    /**
     * Get user's latest news array
     *
     * @return array
     */
    public function latest()
    {
        $data = ["options" => ['allow_state' => true]];
        $query = Request::createQuery($data);
        $response = $this->request->exec(UrlHelper::RESOURCE_GET_LATEST_NEWS . "?{$query}");

        return $this->response->getData($response);
    }
}
