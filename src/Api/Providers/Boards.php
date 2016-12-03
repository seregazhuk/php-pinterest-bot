<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Generator;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

class Boards extends Provider
{
    use CanBeDeleted, Searchable, Followable, SendsMessages;

    const BOARD_PRIVACY_PUBLIC = 'public';
    const BOARD_PRIVACY_PRIVATE = 'secret';

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'send',
        'delete',
        'create',
        'follow',
        'unFollow',
    ];

    protected $searchScope  = 'boards';
    protected $entityIdName = 'board_id';
    protected $followersFor = 'board_id';

    protected $followUrl    = UrlBuilder::RESOURCE_FOLLOW_BOARD;
    protected $unFollowUrl  = UrlBuilder::RESOURCE_UNFOLLOW_BOARD;
    protected $deleteUrl    = UrlBuilder::RESOURCE_DELETE_BOARD;
    protected $followersUrl = UrlBuilder::RESOURCE_BOARD_FOLLOWERS;
    
    /**
     * Get boards for user by username.
     *
     * @param string $username
     *
     * @return array|bool
     */
    public function forUser($username)
    {
        return $this->execGetRequest(['username' => $username], UrlBuilder::RESOURCE_GET_BOARDS);
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

        return $this->execGetRequest($requestOptions, UrlBuilder::RESOURCE_GET_BOARD);
    }

    /**
     * Get pins from board by boardId.
     *
     * @param int $boardId
     * @param int $limit
     *
     * @return Generator
     */
    public function pins($boardId, $limit = 0)
    {
        return (new Pagination($limit))
            ->paginateOver(function($bookmarks = []) use ($boardId) {
                return $this->getPinsFromBoard($boardId, $bookmarks);
            });
    }

    /**
     * @param int   $boardId
     * @param array $bookmarks
     *
     * @return PaginatedResponse
     */
    public function getPinsFromBoard($boardId, $bookmarks = [])
    {
        return $this->execGetRequestWithPagination(
            ['board_id' => $boardId],
            UrlBuilder::RESOURCE_GET_BOARD_FEED,
            $bookmarks
        );
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

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_UPDATE_BOARD);
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
    public function create($name, $description, $privacy = self::BOARD_PRIVACY_PUBLIC)
    {
        $requestOptions = [
            'name'        => $name,
            'description' => $description,
            'privacy'     => $privacy,
        ];

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_CREATE_BOARD);
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
        return $this->execGetRequest(['pin_id' => $pinId], UrlBuilder::RESOURCE_TITLE_SUGGESTIONS);
    }

    /**
     * Send board with message or by email.
     *
     * @param int $boardId
     * @param string $text
     * @param array|string $userIds
     * @param array|string $emails
     * @return bool
     */
    public function send($boardId, $text, $userIds, $emails)
    {
        $messageData = $this->buildMessageData($text, $boardId);

        return $this->callSendMessage($userIds, $emails, $messageData);
    }

    /**
     * Send board with messages.
     *
     * @param int $boardId
     * @param string $text
     * @param array|string $userIds
     * @return bool
     */
    public function sendWithMessage($boardId, $text, $userIds)
    {
        return $this->send($boardId, $text, $userIds, []);
    }

    /**
     * Send board with emails.
     *
     * @param int $boardId
     * @param string $text
     * @param array|string $emails
     * @return bool
     */
    public function sendWithEmail($boardId, $text, $emails)
    {
        return $this->send($boardId, $text, [], $emails);
    }
}
