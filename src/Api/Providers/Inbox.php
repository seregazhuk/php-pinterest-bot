<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

class Inbox extends Provider
{
    use SendsMessages;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'news',
        'sendEmail',
        'sendMessage',
        'notifications',
        'conversations',
    ];

    /**
     * @param int $limit
     * @return Pagination
     */
    public function news($limit = Pagination::DEFAULT_LIMIT)
    {
        $data = ['allow_stale' => true];

        return $this->paginate($data, UrlBuilder::RESOURCE_GET_LATEST_NEWS, $limit);
    }

    /**
     * @param int $limit
     * @return Pagination
     */
    public function notifications($limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate([], UrlBuilder::RESOURCE_GET_NOTIFICATIONS, $limit);
    }

    /**
     * Get last user conversations.
     *
     * @return array|bool
     */
    public function conversations()
    {
        return $this->execGetRequest([], UrlBuilder::RESOURCE_GET_LAST_CONVERSATIONS);
    }

    /**
     * Send message to a user.
     *
     * @param array|string $userIds
     * @param string $text
     * @param int|null $pinId
     * @throws InvalidRequest
     * @return bool
     */
    public function sendMessage($userIds, $text, $pinId = null)
    {
        return $this->send($pinId, $text, $userIds, []);
    }

    /**
     * Send email.
     *
     * @param array|string $emails
     * @param string $text
     * @param int|null $pinId
     * @throws InvalidRequest
     * @return bool
     */
    public function sendEmail($emails, $text, $pinId = null)
    {
        return $this->send($pinId, $text, [], $emails);
    }
}
