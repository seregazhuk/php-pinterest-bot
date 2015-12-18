<?php
namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Providers\ConversationHelper;

class Conversations extends Provider
{
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
        $this->request->checkLoggedIn();
        $data = array(
            "options" => array(
                "user_ids" => array(
                    $userId,
                ),
                "emails"   => array(),
                "text"     => $text,
            ),
            "context" => new \stdClass(),
        );

        $request = ConversationHelper::createRequestData($data);

        $postString = UrlHelper::buildRequestString($request);
        $res = $this->request->exec(UrlHelper::RESOURCE_SEND_MESSAGE, $postString);

        return ConversationHelper::checkMethodCallResult($res);
    }

    /**
     * Get last user conversations
     * @return array|bool
     */
    public function last()
    {
        $this->request->checkLoggedIn();
        $request = [
            "options" => [],
            "context" => new \stdClass()
        ];
        $data = ConversationHelper::createRequestData($request, '/');
        $query = UrlHelper::buildRequestString($data);
        $res = $this->request->exec(UrlHelper::RESOURCE_GET_LAST_CONVERSATIONS . '?' . $query);
        return ConversationHelper::getDataFromResponse($res);
    }
}
