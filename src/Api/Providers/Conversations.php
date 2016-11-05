<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

class Conversations extends Provider
{
    use SendsMessages;

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
     * @param array|int $userIds
     * @param string $text
     * @param int|null $pinId
     *
     * @throws InvalidRequest
     *
     * @return bool
     */
    public function sendMessage($userIds, $text, $pinId = null)
    {
        $messageData = $this->buildMessageData($text, $pinId);

        return $this->callSendMessage($userIds, [], $messageData);
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
        $messageData = $this->buildMessageData($text, $pinId);

        return $this->callSendMessage([], $emails, $messageData);
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
}
