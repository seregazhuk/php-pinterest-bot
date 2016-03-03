<?php
namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Conversations extends Provider
{
    protected $loginRequired = ['last', 'sendMessage', 'sendEmail'];

    /**
     * Send message to a user
     *
     * @param array|int $userId
     * @param string $text
     * @param int|null $pinId
     * @return bool
     *
     * @throws InvalidRequestException
     */
    public function sendMessage($userId = [], $text, $pinId = null)
    {
        $userId = is_array($userId) ? $userId : array($userId);

        return $this->callSendMessage($userId, $text, $pinId);
    }

    /**
     * Send email
     *
     * @param array|int $emails
     * @param string $text
     * @param int|null $pinId
     * @return bool
     * @throws InvalidRequestException
     */
    public function sendEmail($emails = [], $text, $pinId = null)
    {
        $emails = is_array($emails) ? $emails : array($emails);

        return $this->callSendMessage([], $text, $pinId, $emails);
    }

    /**
     * Get last user conversations
     *
     * @return array|bool
     */
    public function last()
    {
        $response = $this->request->exec(
            UrlHelper::RESOURCE_GET_LAST_CONVERSATIONS.'?'.Request::createQuery()
        );

        return $this->response->getData($response);
    }

    /**
     * @param array $userId
     * @param string $text
     * @param int $pinId
     * @param array $emails
     * @return bool
     *
     * @throws InvalidRequestException
     */
    protected function callSendMessage($userId = [], $text, $pinId, $emails = [])
    {
        if (empty($userId) && empty($emails)) {
            throw new InvalidRequestException('You must specify user_ids or emails to send message.');
        }

        $requestOptions = array(
            'pin'      => $pinId,
            'text'     => $text,
            'emails'   => $emails,
            'user_ids' => $userId,
        );

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_SEND_MESSAGE);
    }
}
