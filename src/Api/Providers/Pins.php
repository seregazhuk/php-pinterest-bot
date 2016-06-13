<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Searchable;

class Pins extends Provider
{
    use Searchable;

    protected $loginRequired = [
        'like',
        'unLike',
        'comment',
        'deleteComment',
        'create',
        'repin',
        'delete',
        'activity'
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
        return $this->execPostRequest(['pin_id' => $pinId], $resourceUrl);
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

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_PIN, true);
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

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_COMMENT_DELETE_PIN);
    }

    /**
     * Create a pin. Returns created pin ID.
     *
     * @param string $imageUrl
     * @param int    $boardId
     * @param string $description
     * @param string $link
     *
     * @return bool|int
     */
    public function create($imageUrl, $boardId, $description = '', $link = '')
    {
        $requestOptions = [
            'method'      => 'scraped',
            'description' => $description,
            'link'        => empty($link) ? $imageUrl : $link,
            'image_url'   => $imageUrl,
            'board_id'    => $boardId,
        ];

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_CREATE_PIN, true);
    }

    /**
     * Edit pin by ID. You can move pin to a new board by setting this board id.
     *
     * @param int $pindId
     * @param string $description
     * @param string $link
     * @param int|null $boardId
     * @return mixed
     */
    public function edit($pindId, $description = '', $link = '', $boardId = null)
    {
        $requestOptions = [
            'id'          => $pindId,
            'description' => $description,
            'link'        => $link,
            'board_id'    => $boardId,
        ];

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_UPDATE_PIN, true);
    }

    /**
     * Moves pin to a new board
     *
     * @param int $pindId
     * @param int $boardId
     * @return mixed
     */
    public function moveToBoard($pindId, $boardId)
    {
        return $this->edit($pindId, '', '', $boardId);
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

        return $this->execPostRequest($requestOptions, UrlHelper::RESOURCE_REPIN, true);
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
        return $this->execPostRequest(['id' => $pinId], UrlHelper::RESOURCE_DELETE_PIN);
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
            'id'            => $pinId,
            'field_set_key' => 'detailed',
        ];

        return $this->execGetRequest($requestOptions, UrlHelper::RESOURCE_PIN_INFO);
    }

    /**
     * Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will return
     * recent Pins from flickr.com
     *
     * @param string $source
     * @param int $limit
     * @return Iterator
     */
    public function fromSource($source, $limit = 0)
    {
        $params = [
            'data' => ['domain' => $source],
            'url'  => UrlHelper::RESOURCE_DOMAIN_FEED,
        ];

        return (new Pagination($this))->paginate('getPaginatedData', $params, $limit);
    }

    /**
     * @param $pinId
     * @param int $limit
     * @return Iterator
     */
    public function activity($pinId, $limit = 0)
    {
        $pinInfo = $this->info($pinId);
        if (!isset($pinInfo['aggregated_pin_data']['id'])) {
            return null;
        }

        $aggregatedPinId = $pinInfo['aggregated_pin_data']['id'];
        $params = [
            'data' => ['aggregated_pin_data_id' => $aggregatedPinId],
            'url'  => UrlHelper::RESOURCE_ACTIVITY
        ];

        return (new Pagination($this))->paginate('getPaginatedData', $params, $limit);
    }

    /**
     * @return string
     */
    protected function getScope()
    {
        return 'pins';
    }
}
