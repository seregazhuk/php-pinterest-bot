<?php

namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Request;
use seregazhuk\PinterestBot\Helpers\BoardHelper;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\PaginationHelper;

class Boards extends Provider
{

    /**
     * Get all logged-in user boards
     *
     * @return array|null
     */
    public function my()
    {
        $this->request->checkLoggedIn();

        $get       = BoardHelper::createBoardsInfoRequest();
        $getString = UrlHelper::buildRequestString($get);
        $res       = $this->request->exec(UrlHelper::RESOURCE_GET_BOARDS . "?{$getString}");

        return BoardHelper::parseBoardsInfoResponse($res);
    }


    /**
     * Search boards by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function search($query, $batchesLimit = 0)
    {
        return $this->request->searchWithPagination($query, Request::SEARCH_BOARDS_SCOPES, $batchesLimit);
    }


    /**
     * Follow board by boardID
     *
     * @param $boardId
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
     * @param $boardId
     * @return bool
     */
    public function unFollow($boardId)
    {
        $this->request->checkLoggedIn();

        return $this->request->followMethodCall($boardId, Request::BOARD_ENTITY_ID, UrlHelper::RESOURCE_UNFOLLOW_BOARD);
    }
}