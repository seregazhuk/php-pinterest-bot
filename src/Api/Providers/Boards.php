<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\BoardInvites;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Api\Traits\ResolvesCurrentUsername;
use seregazhuk\PinterestBot\Api\Providers\Core\FollowableProvider;

class Boards extends FollowableProvider
{
    use CanBeDeleted, Searchable, SendsMessages, ResolvesCurrentUsername, BoardInvites;

    const BOARD_PRIVACY_PUBLIC = 'public';
    const BOARD_PRIVACY_PRIVATE = 'secret';

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'my',
        'create',
    ];

    protected $searchScope  = 'boards';
    protected $entityIdName = 'board_id';
    protected $followersFor = 'board_id';

    protected $followUrl    = UrlBuilder::RESOURCE_FOLLOW_BOARD;
    protected $unFollowUrl  = UrlBuilder::RESOURCE_UNFOLLOW_BOARD;
    protected $deleteUrl    = UrlBuilder::RESOURCE_DELETE_BOARD;
    protected $followersUrl = UrlBuilder::RESOURCE_BOARD_FOLLOWERS;

    protected $messageEntityName = 'board';

    /**
     * Get boards for user by username.
     *
     * @param string $username
     *
     * @return array
     */
    public function forUser($username)
    {
        $options = [
            'username'      => $username,
            'field_set_key' => 'detailed',
        ];

        $result = $this->get($options, UrlBuilder::RESOURCE_GET_BOARDS);

        return $result ?: [];
    }

    /**
     * Get boards for current logged in user.
     *
     * @return array
     */
    public function forMe()
    {
        $currentUserName = $this->resolveCurrentUsername();

        if (!$currentUserName) return [];

        return $this->forUser($currentUserName);
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
            'slug'          => $this->formatBoardName($board),
            'username'      => $username,
            'field_set_key' => 'detailed',
        ];

        return $this->get($requestOptions, UrlBuilder::RESOURCE_GET_BOARD);
    }

    /**
     * @param string $board
     * @return string
     */
    protected function formatBoardName($board)
    {
        return strtolower(str_replace(' ', '-', $board));
    }

    /**
     * Get pins from board by boardId.
     *
     * @param int $boardId
     * @param int $limit
     *
     * @return Pagination
     */
    public function pins($boardId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            ['board_id' => $boardId],
            UrlBuilder::RESOURCE_GET_BOARD_FEED,
            $limit
        );
    }

    /**
     * Update board info. Gets boardId and an associative array as params. Available keys of the array are:
     * 'name', 'category', 'description', 'privacy'.
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

        return $this->post($requestOptions, UrlBuilder::RESOURCE_UPDATE_BOARD);
    }

    /**
     * Create a new board.
     *
     * @param string $name
     * @param string $description
     * @param string $privacy Can be 'public' or 'secret'. 'public' by default.
     *
     * @return bool
     */
    public function create($name, $description, $privacy = self::BOARD_PRIVACY_PUBLIC)
    {
        $requestOptions = [
            'name'        => $name,
            'description' => $description,
            'privacy'     => $privacy,
        ];

        return $this->post($requestOptions, UrlBuilder::RESOURCE_CREATE_BOARD);
    }

    /**
     * Create a new board.
     *
     * @param string $name
     * @param string $description
     *
     * @return bool
     */
    public function createPrivate($name, $description)
    {
        return $this->create($name, $description, self::BOARD_PRIVACY_PRIVATE);
    }

    /**
     * Returns title suggestions for pin.
     *
     * @param string $pinId
     * @return array|bool
     */
    public function titleSuggestionsFor($pinId)
    {
        return $this->get(['pin_id' => $pinId], UrlBuilder::RESOURCE_TITLE_SUGGESTIONS);
    }
}
