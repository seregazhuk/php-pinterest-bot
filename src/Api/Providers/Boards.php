<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\FollowTrait;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\SearchTrait;

class Boards extends Provider
{
    use SearchTrait, FollowTrait;

    /**
     * Get boards for user by username
     * @param string $username
     * @return array|bool
     */
    public function forUser($username)
    {
        $get = Request::createRequestData(['options' => ["username" => $username]]);

        return $this->boardsGetCall($get, UrlHelper::RESOURCE_GET_BOARDS);
    }

    /**
     * Get info about user's board
     * @param string $username
     * @param string $board
     * @return array|bool
     */
    public function info($username, $board)
    {
        $get = Request::createRequestData(
            [
                'options' => [
                    'username'      => $username,
                    'slug'          => $board,
                    'field_set_key' => 'detailed'
                ],
            ]
        );

        return $this->boardsGetCall($get, UrlHelper::RESOURCE_GET_BOARDS);
    }

    /**
     * Get pins form board by boardId
     * @param int $boardId
     * @param int $batchesLimit
     * @return \Iterator
     */
    public function pins($boardId, $batchesLimit = 0)
    {
        return $this->getPaginatedData(
            [$this, 'getPinsFromBoard'], [
            'boardId' => $boardId,
        ], $batchesLimit
        );

    }

    /**
     * @param int $boardId
     * @param array $bookmarks
     * @return array|bool
     */
    protected function getPinsFromBoard($boardId, $bookmarks = [])
    {
        $get = Request::createRequestData(
            ['options' => ['board_id' => $boardId]], '', $bookmarks
        );

        return $this->boardsGetCall($get, UrlHelper::RESOURCE_GET_BOARD_FEED, true);
    }

    /**
     * Run GET api request to boards resource
     * @param array $query
     * @param string $url
     * @param bool $pagination
     * @return array|bool
     */
    protected function boardsGetCall($query, $url, $pagination = false)
    {
        $getString = UrlHelper::buildRequestString($query);
        $response = $this->request->exec($url . "?{$getString}");
        if ($pagination) {
            return $this->response->getPaginationData($response);
        }

        return $this->response->getData($response);
    }

    /**
     * Delete your board by ID
     *
     * @param int $boardId
     * @return array|bool
     */
    public function delete($boardId)
    {
        return $this->callPostRequest(['board_id' => $boardId], UrlHelper::RESOURCE_DELETE_BOARD, true);
    }

    /**
     * Create a new board
     *
     * @param string $name
     * @param string $description
     * @param string $privacy Can be 'public' or 'secret'. 'public by default.
     * @return array|bool
     */
    public function create($name, $description, $privacy = 'public')
    {
        $requestOptions = [
            'name'        => $name,
            'description' => $description,
            'privacy'     => $privacy,
        ];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_CREATE_BOARD, true);
    }

    /**
     * Search scope
     *
     * @return string
     */
    protected function getScope()
    {
        return 'boards';
    }

    protected function getEntityIdName()
    {
        return Request::BOARD_ENTITY_ID;
    }

    /**
     * Follow resource
     *
     * @return string
     */
    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_BOARD;
    }

    /**
     * UnFollow resource
     * @return string
     */
    protected function getUnfFollowUrl()
    {
        return UrlHelper::RESOURCE_UNFOLLOW_BOARD;
    }
}