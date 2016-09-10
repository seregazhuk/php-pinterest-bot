<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class Pins extends Provider
{
    use Searchable, CanBeDeleted, UploadsImages;

    protected $loginRequiredFor = [
        'like',
        'unLike',
        'comment',
        'deleteComment',
        'create',
        'repin',
        'delete',
        'activity',
        'userFeed'
    ];

    protected $searchScope  = 'pins';
    protected $entityIdName = 'id';

    protected $deleteUrl = UrlBuilder::RESOURCE_DELETE_PIN;
    
    /**
     * Likes pin with current ID.
     *
     * @param int $pinId
     *
     * @return bool
     */
    public function like($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlBuilder::RESOURCE_LIKE_PIN);
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
        return $this->likePinMethodCall($pinId, UrlBuilder::RESOURCE_UNLIKE_PIN);
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
        $requestOptions = ['pin_id' => (string)$pinId, 'text' => $text];

        return $this
            ->execPostRequest($requestOptions, UrlBuilder::RESOURCE_COMMENT_PIN, true)
            ->isOk();
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

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_COMMENT_DELETE_PIN);
    }

    /**
     * Create a pin. Returns created pin info.
     *
     * @param string $imageUrl
     * @param int    $boardId
     * @param string $description
     * @param string $link
     *
     * @return array
     */
    public function create($imageUrl, $boardId, $description = '', $link = '')
    {
        // Upload image if first argument is not url
        if (!filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $imageUrl = $this->upload($imageUrl);
        }

        $requestOptions = [
            'method'      => 'scraped',
            'description' => $description,
            'link'        => empty($link) ? $imageUrl : $link,
            'image_url'   => $imageUrl,
            'board_id'    => $boardId,
        ];

        return $this
            ->execPostRequest($requestOptions, UrlBuilder::RESOURCE_CREATE_PIN, true)
            ->getResponseData();
    }

    /**
     * Edit pin by ID. You can move pin to a new board by setting this board id.
     *
     * @param int $pindId
     * @param string $description
     * @param string $link
     * @param int|null $boardId
     * @return bool
     */
    public function edit($pindId, $description = '', $link = '', $boardId = null)
    {
        $requestOptions = [
            'id'          => $pindId,
            'description' => $description,
            'link'        => $link,
            'board_id'    => $boardId,
        ];

        return $this->execPostRequest($requestOptions, UrlBuilder::RESOURCE_UPDATE_PIN);
    }

    /**
     * Moves pin to a new board
     *
     * @param int $pindId
     * @param int $boardId
     * @return bool
     */
    public function moveToBoard($pindId, $boardId)
    {
        return $this->edit($pindId, '', '', $boardId);
    }
    
    /**
     * Make a repin.
     *
     * @param int|string   $repinId
     * @param int|string   $boardId
     * @param string $description
     *
     * @return array
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

        return $this
            ->execPostRequest($requestOptions, UrlBuilder::RESOURCE_REPIN, true)
            ->getResponseData();
    }

    /**
     * Get information of a pin by PinID.
     *
     * @param int|string $pinId
     *
     * @return array|bool
     */
    public function info($pinId)
    {
        $requestOptions = [
            'id'            => $pinId,
            'field_set_key' => 'detailed',
        ];

        return $this->execGetRequest($requestOptions, UrlBuilder::RESOURCE_PIN_INFO);
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
            'url'  => UrlBuilder::RESOURCE_DOMAIN_FEED,
        ];

        return $this->getPaginatedResponse($params, $limit);
    }

    /**
     * Get the latest pin activity with pagination.
     *
     * @param int|string $pinId
     * @param int $limit
     * @return Iterator|null
     */
    public function activity($pinId, $limit = 0)
    {
        if (!$aggregatedPinId = $this->getAggregatedPinId($pinId)) {
            return null;
        }

        $params = [
            'data' => ['aggregated_pin_data_id' => $aggregatedPinId],
            'url'  => UrlBuilder::RESOURCE_ACTIVITY
        ];

        return $this->getPaginatedResponse($params, $limit);
    }

    /**
     * Get pins from user's feed
     *
     * @param int $limit
     * @return Iterator
     */
    public function userFeed($limit = 0)
    {
        $params = [
            'data' => [],
            'url'  => UrlBuilder::RESOURCE_USER_FEED
        ];

        return $this->getPaginatedResponse($params, $limit);
    }

    /**
     * Calls Pinterest API to like or unlike Pin by ID.
     *
     * @param int $pinId
     * @param string $resourceUrl
     *
     * @return bool
     */
    protected function likePinMethodCall($pinId, $resourceUrl)
    {
        return $this->execPostRequest(['pin_id' => $pinId], $resourceUrl);
    }

    /**
     * @param int $pinId
     * @return int|null
     */
    protected function getAggregatedPinId($pinId)
    {
        $pinInfo = $this->info($pinId);

        return isset($pinInfo['aggregated_pin_data']['id']) ?
            $pinInfo['aggregated_pin_data']['id'] :
            null;
    }
}
