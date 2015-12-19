<?php

namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Request;
use seregazhuk\PinterestBot\Helpers\Providers\BoardHelper;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Boards extends Provider
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
        $res = $this->request->exec(UrlHelper::RESOURCE_GET_BOARDS . "?{$getString}");

        return BoardHelper::getDataFromResponse($res, 'all_boards');
    }


    /**
     * Search boards by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public function search($query, $batchesLimit = 0)
    {
        return $this->request->searchWithPagination($query, Request::SEARCH_BOARDS_SCOPES, $batchesLimit);
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

        return $this->request->followMethodCall($boardId, Request::BOARD_ENTITY_ID, UrlHelper::RESOURCE_FOLLOW_BOARD);
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

        return $this->request->followMethodCall($boardId, Request::BOARD_ENTITY_ID, UrlHelper::RESOURCE_UNFOLLOW_BOARD);
    }
}