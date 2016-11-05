<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Exceptions\InvalidRequest;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait SendsMessages
{
    use HandlesRequest;

    /**
     * @param array|int $userIds
     * @param array|string $emails
     *
     * @param array $data
     * @return bool
     */
    protected function callSendMessage($userIds, $emails, array $data)
    {
        $userIds = is_array($userIds) ? $userIds : [$userIds];
        $emails = is_array($emails) ? $emails : [$emails];

        $this->guardAgainstEmptyData($userIds, $emails);

        $requestOptions = array_merge([
                'emails'   => $emails,
                'user_ids' => $userIds,
            ],
            $data);

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_SEND_MESSAGE);
    }

    /**
     * @param string $text
     * @param string $pinId
     * @param string $boardId
     * @return array
     */
    protected function buildMessageData($text = null, $pinId = null, $boardId = null)
    {
        return [
            'pin'   => $pinId,
            'text'  => $text,
            'board' => $boardId,
        ];
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