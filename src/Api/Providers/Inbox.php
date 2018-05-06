<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

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
        'contactRequests',
        'conversations',
    ];

    /**
     * @param int $limit
     * @return Pagination
     */
    public function news($limit = Pagination::DEFAULT_LIMIT)
    {
        $data = ['allow_stale' => true];

        return $this->paginate(UrlBuilder::RESOURCE_GET_LATEST_NEWS, $data, $limit);
    }

    /**
     * @param int $limit
     * @return Pagination
     */
    public function notifications($limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(UrlBuilder::RESOURCE_GET_NOTIFICATIONS, [], $limit);
    }

    /**
     * Get last user conversations.
     *
     * @param int $limit
     * @return Pagination
     */
    public function conversations($limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(UrlBuilder::RESOURCE_GET_LAST_CONVERSATIONS, [], $limit);
    }

    /**
     * @param int $conversationId
     * @param int $limit
     * @return Pagination
     */
    public function getMessages($conversationId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            UrlBuilder::RESOURCE_GET_CONVERSATION_MESSAGES, ['conversation_id' => (string)$conversationId], $limit
        );
    }

    /**
     * Send message to a user.
     *
     * @param array|string $userIds
     * @param string $text
     * @param int|null $pinId
     * @return bool
     * @throws InvalidRequest
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
     * @return bool
     * @throws InvalidRequest
     */
    public function sendEmail($emails, $text, $pinId = null)
    {
        return $this->send($pinId, $text, [], $emails);
    }

    /**
     * Get current contact requests.
     *
     * @return array
     */
    public function contactRequests()
    {
        $requests = $this->get(UrlBuilder::RESOURCE_CONTACTS_REQUESTS);

        return !$requests ? [] : $requests;
    }

    /**
     * @param string $requestId
     * @return bool
     */
    public function acceptContactRequest($requestId)
    {
        return $this->makeContactRequestCall(
            $requestId, UrlBuilder::RESOURCE_CONTACT_REQUEST_ACCEPT
        );
    }

    /**
     * @param string $requestId
     * @return bool
     */
    public function ignoreContactRequest($requestId)
    {
        return $this->makeContactRequestCall(
            $requestId, UrlBuilder::RESOURCE_CONTACT_REQUEST_IGNORE
        );
    }

    /**
     * @param string $newsId
     * @param int $limit
     * @return Pagination
     */
    public function newsHubDetails($newsId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            UrlBuilder::RESOURCE_GET_NEWS_HUB_DETAILS,
            ['news_id' => $newsId],
            $limit
        );
    }

    /**
     * @param string $requestId
     * @param $endpoint
     * @return bool
     */
    protected function makeContactRequestCall($requestId, $endpoint)
    {
        $data = [
            'contact_request' => [
                'type' => 'contactrequest',
                'id'   => $requestId,
            ],
        ];

        return $this->post($endpoint, $data);
    }
}
