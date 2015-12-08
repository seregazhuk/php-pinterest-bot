<?php
namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

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
                "emails" => array(),
                "text" => $text,
            ),
            "context" => new \stdClass(),
        );

        $dataJson = json_encode($data);

        $post = array(
            'data' => $dataJson,
        );

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->request->exec(UrlHelper::RESOURCE_SEND_MESSAGE, $postString);

        if ($res === null || !isset($res['resource_response']) || $res['resource_response']['error'] !== null) {
            return false;
        }

        return true;
    }
}
