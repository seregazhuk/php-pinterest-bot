<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Providers\SearchHelper;

class Pins extends Provider
{
    use SearchHelper;

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
     * Calls Pinterest API to like or unlike Pin by ID
     *
     * @param int    $pinId
     * @param string $resourceUrl
     * @return bool
     */
    protected function likePinMethodCall($pinId, $resourceUrl)
    {
        return $this->callPostRequest(['pin_id' => $pinId], $resourceUrl, true);
    }

    /**
     * Writes comment for pin with current id
     *
     * @param integer $pinId
     * @param string  $text Comment
     * @return array|bool
     */
    public function comment($pinId, $text)
    {
        $requestOptions = ['pin_id' => $pinId, 'test' => $text];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_PIN, true, true);
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
        $requestOptions = ["pin_id" => $pinId, "comment_id" => $commentId];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_DELETE_PIN, true);
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
        $requestOptions = [
            "method"      => "scraped",
            "description" => $description,
            "link"        => $imageUrl,
            "image_url"   => $imageUrl,
            "board_id"    => $boardId,
        ];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_CREATE_PIN, true, true);
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
        $requestOptions = [
            "board_id"    => $boardId,
            "description" => stripslashes($description),
            "link"        => stripslashes($repinId),
            "is_video"    => null,
            "pin_id"      => $repinId,
        ];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_REPIN, true, true);
    }

    /**
     * Delete pin
     *
     * @param int $pinId
     * @return bool
     */
    public function delete($pinId)
    {
        return $this->callPostRequest(['id' => $pinId], UrlHelper::RESOURCE_DELETE_PIN, true);
    }

    /**
     * Get information of pin by PinID
     *
     * @param $pinId
     * @return array|bool
     */
    public function info($pinId)
    {
        $requestOptions = [
            "field_set_key" => "detailed",
            "id"            => $pinId,
            "pin_id"        => $pinId,
            "allow_stale"   => true
        ];

        $data = array("options" => $requestOptions);
        $request = Request::createRequestData($data);

        $url = UrlHelper::RESOURCE_PIN_INFO.'?'.UrlHelper::buildRequestString($request);
        $response = $this->request->exec($url);

        return $this->response->checkResponse($response);
    }

    /**
     * @return string
     */
    protected function getScope()
    {
        return 'pins';
    }
}
