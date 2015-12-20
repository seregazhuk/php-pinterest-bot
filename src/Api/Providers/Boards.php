<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\SearchHelper;
use seregazhuk\PinterestBot\Helpers\Requests\BoardHelper;

class Boards extends SearchProvider
{
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

    /**
     * Follow board by boardID
     *
     * @param int $boardId
     * @return bool
     */
    public function follow($boardId)
    {
        $this->request->checkLoggedIn();

        $response = $this->request->followMethodCall($boardId, Request::BOARD_ENTITY_ID, UrlHelper::RESOURCE_FOLLOW_BOARD);
        return $this->response->checkResponse($response);
    }

    /**
     * Unfollow board by boardID
     *
     * @param int $boardId
     * @return bool
     */
    public function unFollow($boardId)
    {
        $this->request->checkLoggedIn();

        $response = $this->request->followMethodCall($boardId, Request::BOARD_ENTITY_ID, UrlHelper::RESOURCE_UNFOLLOW_BOARD);
        return $this->response->checkResponse($response);
    }

    protected function getScope()
    {
        return 'boards';
    }
}