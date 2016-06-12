<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Searchable;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Followable;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\HasFollowers;

class Boards extends Provider
{
    use Searchable, Followable, HasFollowers;

    protected $loginRequired = [
        'delete',
        'create',
        'follow',
        'unFollow',
    ];

    /**
     * Get boards for user by username.
     *
     * @param string $username
     *
     * @return array|bool
     */
    public function forUser($username)
    {
        return $this->execGetRequest(['username' => $username], UrlHelper::RESOURCE_GET_BOARDS);
    }

    /**
     * Get info about user's board.
     *
     * @param string $username
     * @param string $board
     *
     * @return array|bool
     */
    public function info($username, $board)
    {
        $requestOptions = [
            'username'      => $username,
            'slug'          => $board,
            'field_set_key' => 'detailed',
        ];

        return $this->execGetRequest(
            $requestOptions, UrlHelper::RESOURCE_GET_BOARDS
        );
    }

    /**
     * Get pins from board by boardId.
     *
     * @param int $boardId
     * @param int $batchesLimit
     *
     * @return Iterator
     */
    public function pins($boardId, $batchesLimit = 0)
    {
        return (new Pagination($this))->paginate(
            'getPinsFromBoard',
            ['boardId' => $boardId],
            $batchesLimit
        );
    }

    /**
     * @param int   $boardId
     * @param array $bookmarks
     *
     * @return array|bool
     */
    public function getPinsFromBoard($boardId, $bookmarks = [])
    {
        return $this->execGetRequest(
            ['board_id' => $boardId], UrlHelper::RESOURCE_GET_BOARD_FEED, true, $bookmarks
        );
    }

    /**
     * Delete your board by ID.
     *
     * @param int $boardId
     *
     * @return bool
     */
    public function delete($boardId)
    {
        return $this->execPostRequest(['board_id' => $boardId], UrlHelper::RESOURCE_DELETE_BOARD);
    }

    /**
     * Update board info. Gets boardId and an associative array as params. Available keys of the array are:
     * 'category', 'description', 'privacy'.
     *
     * - 'privacy' can be 'public' or 'secret'. 'public' by default.
     * - 'category' is 'other' by default.
     *
     * @param $boardId
     * @param $attributes
     * @return mixed
     */
    public function update($boardId, $attributes)
    {
        $requestOptions = array_merge(
            [
                'board_id' => $boardId,
                'category' => 'other',
            ], $attributes
        );

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_UPDATE_BOARD);
    }

    /**
     * Create a new board.
     *
     * @param string $name
     * @param string $description
     * @param string $privacy     Can be 'public' or 'secret'. 'public' by default.
     *
     * @return bool
     */
    public function create($name, $description, $privacy = 'public')
    {
        $requestOptions = [
            'name'        => $name,
            'description' => $description,
            'privacy'     => $privacy,
        ];

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_CREATE_BOARD);
    }

    /**
     * Get board followers.
     *
     * @param $boardId
     * @param int $batchesLimit
     *
     * @return Iterator
     */
    public function followers($boardId, $batchesLimit = 0)
    {
        return $this->getFollowData(
            ['board_id' => $boardId], UrlHelper::RESOURCE_BOARD_FOLLOWERS, $batchesLimit
        );
    }

    /**
     * Search scope.
     *
     * @return string
     */
    protected function getScope()
    {
        return 'boards';
    }

    protected function getEntityIdName()
    {
        return 'board_id';
    }

    /**
     * Follow resource.
     *
     * @return string
     */
    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_BOARD;
    }

    /**
     * UnFollow resource.
     *
     * @return string
     */
    protected function getUnfFollowUrl()
    {
        return UrlHelper::RESOURCE_UNFOLLOW_BOARD;
    }
}
