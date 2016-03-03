<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\SearchTrait;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Pins extends Provider
{
    use SearchTrait;

    protected $loginRequired = [
        'like',
        'unLike',
        'comment',
        'deleteComment',
        'create',
        'repin',
        'delete',
    ];

    /**
     * Likes pin with current ID.
     *
     * @param int $pinId
     *
     * @return bool
     */
    public function like($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_LIKE_PIN);
    }

    /**
     * Removes your like from pin with current ID.
     *
     * @param int $pinId
     *
     * @return bool
     */
    public function unLike($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_UNLIKE_PIN);
    }

    /**
     * Calls Pinterest API to like or unlike Pin by ID.
     *
     * @param int    $pinId
     * @param string $resourceUrl
     *
     * @return bool
     */
    protected function likePinMethodCall($pinId, $resourceUrl)
    {
        return $this->callPostRequest(['pin_id' => $pinId], $resourceUrl);
    }

    /**
     * Write a comment for a pin with current id.
     *
     * @param int    $pinId
     * @param string $text  Comment
     *
     * @return array|bool
     */
    public function comment($pinId, $text)
    {
        $requestOptions = ['pin_id' => $pinId, 'test' => $text];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_PIN, true);
    }

    /**
     * Delete a comment for a pin with current id.
     *
     * @param int $pinId
     * @param int $commentId
     *
     * @return bool
     */
    public function deleteComment($pinId, $commentId)
    {
        $requestOptions = ['pin_id' => $pinId, 'comment_id' => $commentId];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_DELETE_PIN);
    }

    /**
     * Create a pin. Returns created pin ID.
     *
     * @param string $imageUrl
     * @param int    $boardId
     * @param string $description
     *
     * @return bool|int
     */
    public function create($imageUrl, $boardId, $description = '')
    {
        $requestOptions = [
            'method'      => 'scraped',
            'description' => $description,
            'link'        => $imageUrl,
            'image_url'   => $imageUrl,
            'board_id'    => $boardId,
        ];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_CREATE_PIN, true);
    }

    /**
     * Make a repin.
     *
     * @param int    $repinId
     * @param int    $boardId
     * @param string $description
     *
     * @return bool|int
     */
    public function repin($repinId, $boardId, $description = '')
    {
        $requestOptions = [
            'board_id'    => $boardId,
            'description' => stripslashes($description),
            'link'        => stripslashes($repinId),
            'is_video'    => null,
            'pin_id'      => $repinId,
        ];

        return $this->callPostRequest($requestOptions, UrlHelper::RESOURCE_REPIN, true);
    }

    /**
     * Delete a pin.
     *
     * @param int $pinId
     *
     * @return bool
     */
    public function delete($pinId)
    {
        return $this->callPostRequest(['id' => $pinId], UrlHelper::RESOURCE_DELETE_PIN);
    }

    /**
     * Get information of a pin by PinID.
     *
     * @param int $pinId
     *
     * @return array|bool
     */
    public function info($pinId)
    {
        $requestOptions = [
            'field_set_key' => 'detailed',
            'id'            => $pinId,
            'pin_id'        => $pinId,
            'allow_stale'   => true,
        ];

        $data = ['options' => $requestOptions];
        $url = UrlHelper::RESOURCE_PIN_INFO.'?'.Request::createQuery($data);
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
