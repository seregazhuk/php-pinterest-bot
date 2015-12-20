<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Providers\FollowHelper;
use seregazhuk\PinterestBot\Helpers\Providers\SearchHelper;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Requests\BoardHelper;

class Boards extends Provider
{
    use SearchHelper, FollowHelper;

    /**
     * Get all logged-in user boards
     *
     * @return array|bool
     */
    public function my()
    {
        $this->request->checkLoggedIn();

        $get = BoardHelper::createBoardsInfoRequest();
        $getString = UrlHelper::buildRequestString($get);
        $response = $this->request->exec(UrlHelper::RESOURCE_GET_BOARDS."?{$getString}");

        return $this->response->getData($response, 'all_boards');
    }

    protected function getScope()
    {
        return 'boards';
    }

    protected function getEntityIdName()
    {
        return Request::BOARD_ENTITY_ID;
    }

    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_BOARD;
    }

    protected function getUnfFollowUrl()
    {
        return UrlHelper::RESOURCE_UNFOLLOW_BOARD;
    }
}