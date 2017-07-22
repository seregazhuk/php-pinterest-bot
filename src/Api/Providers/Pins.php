<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Traits\TryIt;
use seregazhuk\PinterestBot\Helpers\FileHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Api\Providers\Core\EntityProvider;

class Pins extends EntityProvider
{
    use Searchable,
        CanBeDeleted,
        SendsMessages,
        TryIt;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'like',
        'feed',
        'copy',
        'move',
        'repin',
        'unLike',
        'create',
        'activity',
        'analytics',
        'visualSimilar',

    ];

    protected $searchScope  = 'pins';
    protected $entityIdName = 'id';

    protected $messageEntityName = 'pin';

    protected $deleteUrl = UrlBuilder::RESOURCE_DELETE_PIN;

    /**
     * Likes pin with current ID.
     * @param string $pinId
     * @return bool
     */
    public function like($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlBuilder::RESOURCE_LIKE_PIN);
    }

    /**
     * Removes your like from pin with current ID.
     * @param string $pinId
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
     * @param int $boardId
     * @param string $description
     * @param string $link
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

        $this->post($requestOptions, UrlBuilder::RESOURCE_CREATE_PIN);

        return $this->response->getResponseData();
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
            'description' => stripslashes($description),

        ];

        if (!is_null($boardId)) $requestOptions['board_id'] = $boardId;

        if (!empty($link)) $requestOptions['link'] = stripslashes($link);

        return $this->post($requestOptions, UrlBuilder::RESOURCE_UPDATE_PIN);
    }

    /**
     * Moves pin to a new board
     *
     * @param int $pinId
     * @param int $boardId
     * @return bool
     */
    public function moveToBoard($pinId, $boardId)
    {
        return $this->edit($pinId, '', '', $boardId);
    }

    /**
     * Make a repin.
     *
     * @param int $repinId
     * @param int $boardId
     * @param string $description
     * @return array
     */
    public function repin($repinId, $boardId, $description = '')
    {
        $requestOptions = [
            'board_id'    => $boardId,
            'description' => stripslashes($description),
            'link'        => '',
            'is_video'    => null,
            'pin_id'      => $repinId,
        ];

        $this->post($requestOptions, UrlBuilder::RESOURCE_REPIN);

        return $this->response->getResponseData();
    }

    /**
     * Get information of a pin by PinID.
     *
     * @param string $pinId
     * @return array|bool
     */
    public function info($pinId)
    {
        $requestOptions = [
            'id'            => $pinId,
            'field_set_key' => 'detailed',
        ];

        return $this->get($requestOptions, UrlBuilder::RESOURCE_PIN_INFO);
    }

    /**
     * Get pins from a specific url. For example: https://pinterest.com/source/flickr.com/ will
     * return recent Pins from flickr.com
     *
     * @param string $source
     * @param int $limit
     * @return Pagination
     */
    public function fromSource($source, $limit = Pagination::DEFAULT_LIMIT)
    {
        $data = ['domain' => $source];

        return $this->paginate($data, UrlBuilder::RESOURCE_DOMAIN_FEED, $limit);
    }

    /**
     * Get the latest pin activity with pagination.
     *
     * @param string $pinId
     * @param int $limit
     * @return Pagination
     */
    public function activity($pinId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->getAggregatedActivity($pinId, [], $limit);
    }

    /**
     * @param string $pinId
     * @param array $additionalData
     * @param int $limit
     * @return Pagination
     */
    protected function getAggregatedActivity($pinId, $additionalData = [], $limit)
    {
        $aggregatedPinId = $this->getAggregatedPinId($pinId);

        if (is_null($aggregatedPinId)) return new Pagination();

        $additionalData['aggregated_pin_data_id'] = $aggregatedPinId;

        return $this->paginate($additionalData, UrlBuilder::RESOURCE_ACTIVITY, $limit);
    }

    /**
     * Get pins from user's feed
     *
     * @param int $limit
     * @return Pagination
     */
    public function feed($limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate([], UrlBuilder::RESOURCE_USER_FEED, $limit);
    }

    /**
     * @param string $pinId
     * @param int $limit
     * @return Pagination
     */
    public function related($pinId, $limit = Pagination::DEFAULT_LIMIT)
    {
        $requestData = [
            'pin'      => $pinId,
            'add_vase' => true,
        ];

        return $this->paginate($requestData, UrlBuilder::RESOURCE_RELATED_PINS, $limit);
    }

    /**
     * Copy pins to board
     *
     * @param array|string $pinIds
     * @param int $boardId
     * @return bool|Response
     */
    public function copy($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_COPY);
    }

    /**
     * Delete pins from board.
     *
     * @param string|array $pinIds
     * @param int $boardId
     * @return bool
     */
    public function deleteFromBoard($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_DELETE);
    }

    /**
     * Move pins to board
     *
     * @param string|array $pinIds
     * @param int $boardId
     * @return bool|Response
     */
    public function move($pinIds, $boardId)
    {
        return $this->bulkEdit($pinIds, $boardId, UrlBuilder::RESOURCE_BULK_MOVE);
    }

    /**
     * @param string $pinId
     * @param array $crop
     * @return array|bool
     */
    public function visualSimilar($pinId, array $crop = [])
    {
        $data = [
            'pin_id'          => $pinId,
            'crop'            => $crop ?: [
                "x"                => 0.16,
                "y"                => 0.16,
                "w"                => 0.66,
                "h"                => 0.66,
                "num_crop_actions" => 1,
            ],
            'force_refresh'   => true,
            'keep_duplicates' => false,
        ];

        return $this->get($data, UrlBuilder::RESOURCE_VISUAL_SIMILAR_PINS);
    }

    /**
     * Saves the pin original image to the specified path. On success
     * returns full path to saved image. Otherwise returns false.
     *
     * @param string $pinId
     * @param string $path
     * @return false|string
     */
    public function saveOriginalImage($pinId, $path)
    {
        $pinInfo = $this->info($pinId);
        if (!isset($pinInfo['images']['orig']['url'])) return false;

        $originalUrl = $pinInfo['images']['orig']['url'];
        $destination = $path . DIRECTORY_SEPARATOR . basename($originalUrl);

        FileHelper::saveTo($originalUrl, $destination);

        return $destination;
    }

    /**
     * @param string $query
     * @param int $limit
     * @return Pagination
     */
    public function searchInMyPins($query, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginateCustom(
            function () use ($query) {
                return $this->execSearchRequest($query, 'my_pins');
            }
        )->take($limit);
    }

    /**
     * Returns trending pins from http://pinterest.com/discover page. Uses topic id, that can be received
     * from $bot->topics->explore() method.
     *
     * @param string $topicId
     * @param int $limit
     * @return Pagination
     */
    public function explore($topicId, $limit = Pagination::DEFAULT_LIMIT)
    {
        $data = [
            "aux_fields" => [],
            "prepend"    => false,
            "offset"     => 180,
            "section_id" => $topicId,
        ];

        return $this->paginate($data, UrlBuilder::RESOURCE_EXPLORE_PINS, $limit);
    }

    /**
     * Get pin analytics, like numbers of clicks, views and repins
     * @param $pinId
     * @return array|bool|Response
     */
    public function analytics($pinId)
    {
        return $this->get(['pin_id' => $pinId], UrlBuilder::RESOURCE_PIN_ANALYTICS);
    }

    /**
     * Calls Pinterest API to like or unlike Pin by ID.
     *
     * @param string $pinId
     * @param string $resourceUrl
     * @return bool
     */
    protected function likePinMethodCall($pinId, $resourceUrl)
    {
        return $this->post(['pin_id' => $pinId], $resourceUrl);
    }

    /**
     * @param string $pinId
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
     * @param string|array $pinIds
     * @param int $boardId
     * @param string $editUrl
     * @return bool
     */
    protected function bulkEdit($pinIds, $boardId, $editUrl)
    {
        $pinIds = is_array($pinIds) ? $pinIds : [$pinIds];

        $data = [
            'board_id' => $boardId,
            'pin_ids'  => $pinIds,
        ];

        return $this->post($data, $editUrl);
    }
}
