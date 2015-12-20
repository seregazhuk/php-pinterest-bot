<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\SearchHelper;
use seregazhuk\PinterestBot\Helpers\Requests\PinHelper;

class Pins extends SearchProvider
{
    /**
     * Likes pin with current ID
     *
     * @param integer $pinId
     * @return bool
     */
    public function like($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_LIKE_PIN);
    }

    /**
     * Removes your like from pin with current ID
     *
     * @param integer $pinId
     * @return bool
     */
    public function unLike($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_UNLIKE_PIN);
    }

    /**
     * Calls pinterest API to like or unlike Pin by ID
     *
     * @param $pinId
     * @param $url
     * @return bool
     */
    protected function likePinMethodCall($pinId, $url)
    {
        $this->request->checkLoggedIn();
        $data = PinHelper::createPinIdRequest($pinId);
        $post = PinHelper::createPinRequestData($data);
        $postString = URlHelper::buildRequestString($post);
        $response = $this->request->exec($url, $postString);
        return $this->response->checkErrorInResponse($response);
    }

    /**
     * Writes comment for pin with current id
     *
     * @param integer $pinId
     * @param string  $text Comment
     * @return array
     */
    public function comment($pinId, $text)
    {
        $this->request->checkLoggedIn();
        $post = PinHelper::createCommentRequest($pinId, $text);
        $postString = UrlHelper::buildRequestString($post);
        $response = $this->request->exec(UrlHelper::RESOURCE_COMMENT_PIN, $postString);
        return $this->response->getData($response);
    }

    /**
     * Writes comment for pin with current id
     *
     * @param integer $pinId
     * @param integer $commentId
     * @return bool
     */
    public function deleteComment($pinId, $commentId)
    {
        $this->request->checkLoggedIn();
        $post = PinHelper::createCommentDeleteRequest($pinId, $commentId);
        $postString = UrlHelper::buildRequestString($post);
        $response = $this->request->exec(UrlHelper::RESOURCE_COMMENT_DELETE_PIN, $postString);

        return $this->response->checkErrorInResponse($response);
    }

    /**
     * Create pin. Returns created pin ID
     *
     * @param string $imageUrl
     * @param int    $boardId
     * @param string $description
     * @return bool|int
     */
    public function create($imageUrl, $boardId, $description = "")
    {
        $this->request->checkLoggedIn();
        $post = PinHelper::createPinCreationRequest($imageUrl, $boardId, $description);
        $postString = UrlHelper::buildRequestString($post);
        $res = $this->request->exec(UrlHelper::RESOURCE_CREATE_PIN, $postString);

        return $this->response->getData($res, 'id');
    }

    /**
     * Repin
     *
     * @param int    $repinId
     * @param int    $boardId
     * @param string $description
     * @return bool|int
     */
    public function repin($repinId, $boardId, $description = "")
    {
        $this->request->checkLoggedIn();

        $post = PinHelper::createRepinRequest($repinId, $boardId, $description);
        $postString = UrlHelper::buildRequestString($post);
        $res = $this->request->exec(UrlHelper::RESOURCE_REPIN, $postString);

        return $this->response->getData($res, 'id');
    }

    /**
     * Delete pin
     *
     * @param int $pinId
     * @return bool
     */
    public function delete($pinId)
    {
        $this->request->checkLoggedIn();

        $post = PinHelper::createSimplePinRequest($pinId);
        $postString = UrlHelper::buildRequestString($post);
        $response = $this->request->exec(UrlHelper::RESOURCE_DELETE_PIN, $postString);
        return $this->response->checkResponse($response);
    }

    /**
     * Get information of pin by PinID
     *
     * @param $pinId
     * @return array|bool
     */
    public function info($pinId)
    {
        $get = PinHelper::createInfoRequest($pinId);
        $url = UrlHelper::RESOURCE_PIN_INFO.'?'.UrlHelper::buildRequestString($get);
        $response = $this->request->exec($url);

        return $this->response->checkResponse($response);
    }


    protected function getScope()
    {
        return 'pins';
    }
}
