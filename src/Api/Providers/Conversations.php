<?php
namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Conversations extends Provider
{
    protected $loginRequired = ['last', 'sendMessage'];

    /**
     * Send message to a user
     *
     * @param $userId
     * @param $text
     *
     * @return bool
     */
    public function sendMessage($userId, $text)
    {
        $requestOptions = array(
            "user_ids" => array($userId),
            "emails"   => array(),
            "text"     => $text,
        );

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_SEND_MESSAGE);
    }

    /**
     * Get last user conversations
     *
     * @return array|bool
     */
    public function last()
    {
        $response = $this->request->exec(
            UrlHelper::RESOURCE_GET_LAST_CONVERSATIONS . '?' . Request::createQuery()
        );

        return $this->response->getData($response);
    }
}
