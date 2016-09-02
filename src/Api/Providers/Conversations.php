<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

class Conversations extends Provider
{
    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'last',
        'sendMessage',
        'sendEmail'
    ];

    /**
     * Send message to a user.
     *
     * @param array|int $userId
     * @param string $text
     * @param int|null $pinId
     *
     * @throws InvalidRequest
     *
     * @return bool
     */
    public function sendMessage($userId, $text, $pinId = null)
    {
        $userId = is_array($userId) ? $userId : [$userId];

        return $this->callSendMessage($userId, $text, $pinId);
    }

    /**
     * Send email.
     *
     * @param array|int $emails
     * @param string $text
     * @param int|null $pinId
     *
     * @throws InvalidRequest
     *
     * @return bool
     */
    public function sendEmail($emails, $text, $pinId = null)
    {
        $emails = is_array($emails) ? $emails : [$emails];

        return $this->callSendMessage([], $text, $pinId, $emails);
    }

    /**
     * Get last user conversations.
     *
     * @return array|bool
     */
    public function last()
    {
        return $this->execGetRequest([], UrlBuilder::RESOURCE_GET_LAST_CONVERSATIONS);
    }

    /**
     * @param array|int $userId
     * @param string $text
     * @param int $pinId
     * @param array $emails
     *
     * @throws InvalidRequest
     *
     * @return bool
     */
    protected function callSendMessage($userId, $text, $pinId, array $emails = [])
    {
        $this->guardAgainstEmptyData($userId, $emails);

        $requestOptions = [
            'pin'      => $pinId,
            'text'     => $text,
            'emails'   => $emails,
            'user_ids' => $userId,
        ];

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_SEND_MESSAGE);
    }

    /**
     * @param $userId
     * @param array $emails
     * @throws InvalidRequest
     */
    protected function guardAgainstEmptyData($userId, array $emails)
    {
        if (empty($userId) && empty($emails)) {
            throw new InvalidRequest('You must specify user_ids or emails to send message.');
        }
    }
}
