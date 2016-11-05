<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Traits\HasFeed;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class Pins extends Provider
{
    use Searchable, CanBeDeleted, UploadsImages, HasFeed, SendsMessages;

    protected $loginRequiredFor = [
        'like',
        'unLike',
        'create',
        'repin',
        'copy',
        'move',
        'delete',
        'activity',
        'feed',
        'send',
        'visualSimilar',
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
     * @param int   $repinId
     * @param int   $boardId
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

        return $this->execGetRequest($requestOptions, UrlBuilder::RESOURCE_PIN_INFO);
    }

    /**
     * Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will
     * return recent Pins from flickr.com
     *
     * @param string $source
     * @param int $limit
     * @return Iterator
     */
    public function fromSource($source, $limit = 0)
    {
        $data = ['domain' => $source];

        return $this->getFeed($data, UrlBuilder::RESOURCE_DOMAIN_FEED, $limit);
    }

    /**
     * Get the latest pin activity with pagination.
     *
     * @param int $pinId
     * @param int $limit
     * @return Iterator|null
     */
    public function activity($pinId, $limit = 0)
    {
        $aggregatedPinId = $this->getAggregatedPinId($pinId);

        if (is_null($aggregatedPinId)) return null;

        $data = ['aggregated_pin_data_id' => $aggregatedPinId];

        return $this->getFeed($data, UrlBuilder::RESOURCE_ACTIVITY, $limit);
    }

    /**
     * Get pins from user's feed
     *
     * @param int $limit
     * @return Iterator
     */
    public function feed($limit = 0)
    {
        return $this->getFeed([], UrlBuilder::RESOURCE_USER_FEED, $limit);
    }

    /**
     * @param int $pinId
     * @param int $limit
     * @return mixed
     */
    public function related($pinId, $limit = 0)
    {
        return $this->getFeed(['pin' => $pinId], UrlBuilder::RESOURCE_RELATED_PINS, $limit);
    }

    /**
     * @codeCoverageIgnore
     * Copy pins to board
     *
     * @param array|int $pinIds
     * @param int $boardId
     * @return bool|Response
     */
    public function copy($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_COPY);
    }

    /**
     * @codeCoverageIgnore
     * Delete pins from board.
     *
     * @param int|array $pinIds
     * @param int $boardId
     * @return bool
     */
    public function deleteFromBoard($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_DELETE);
    }

    /**
     * Send pin with message or by email.
     *
     * @param int $pinId
     * @param string $text
     * @param array|int $userIds
     * @param array|int $emails
     * @return bool
     */
    public function send($pinId, $text, $userIds, $emails)
    {
        $messageData = $this->buildMessageData($text, $pinId);

        return $this->callSendMessage($userIds, $emails, $messageData);
    }

    /**
     * @codeCoverageIgnore
     * Move pins to board
     *
     * @param int|array $pinIds
     * @param int $boardId
     * @return bool|Response
     */
    public function move($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_MOVE);
    }
    
    /**
     * @param int $pinId
     * @param array $crop
     * @return array|bool
     */
    public function visualSimilar($pinId, array $crop = [])
    {
        $data = [
            'pin_id' => $pinId,
            'crop' => $crop ? : [
                "x" => 0.16,
                "y" => 0.16,
                "w" => 0.66,
                "h" => 0.66,
                "num_crop_actions" => 1
            ],
            'force_refresh' => true,
            'keep_duplicates' => false
        ];

        return $this->execGetRequest($data, UrlBuilder::RESOURCE_VISUAL_SIMILAR_PINS);
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

    /**
     * @param mixed $params
     * @return array
     */
    protected function getFeedRequestData($params = [])
    {
        return ['domain' => $params['source']];
    }

    /**
     * @param int|array $pinIds
     * @param int $boardId
     * @param string $editUrl
     * @return bool
     */
    protected function bulkEdit($pinIds, $boardId, $editUrl)
    {
        $pinIds = is_array($pinIds) ? $pinIds : [$pinIds];

        $data = [
            'board_id' => (string)$boardId,
            'pin_ids'  => $pinIds,
        ];

        return $this->execPostRequest($data, $editUrl);
    }
}
