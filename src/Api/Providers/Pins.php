<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Traits\TryIt;
use seregazhuk\PinterestBot\Helpers\FileHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Api\Traits\CanBeShared;
use seregazhuk\PinterestBot\Api\Traits\CanBeDeleted;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Api\Providers\Core\EntityProvider;

class Pins extends EntityProvider
{
    use Searchable,
        CanBeDeleted,
        SendsMessages,
        TryIt,
        CanBeShared;

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
     * Create a pin. Returns created pin info.
     *
     * @param string $imageUrl
     * @param int $boardId
     * @param string $description
     * @param string $link
     * @param string $title
     * @return array
     */
    public function create($imageUrl, $boardId, $description = '', $link = '', $title = '', $sectionId = null)
    {
        // Upload image if first argument is a local file
        if (file_exists($imageUrl)) {
            $imageUrl = $this->upload($imageUrl);
        }

        $requestOptions = [
            'method' => 'scraped',
            'description' => $description,
            'link' => $link,
            'image_url' => $imageUrl,
            'board_id' => $boardId,
            'title' => $title,
        ];

        if ($sectionId !== null) {
            $requestOptions['section'] = $sectionId;
        }

        $this->post(UrlBuilder::RESOURCE_CREATE_PIN, $requestOptions);

        return $this->response->getResponseData();
    }

    /**
     * Edit pin by ID. You can move pin to a new board by setting this board id.
     *
     * @param int $pindId
     * @param string $description
     * @param string $link
     * @param int|null $boardId
     * @param string $title
     * @param int|null $sectionId
     * @return bool
     */

    public function edit($pindId, $description = '', $link = '', $boardId = null, $title = '', $sectionId = null)
    {
        $requestOptions = ['id' => $pindId];

        if (!empty($description)) {
            $requestOptions['description'] = $description;
        }

        if (!empty($link)) {
            $requestOptions['link'] = stripslashes($link);
        }

        if ($boardId !== null) {
            $requestOptions['board_id'] = $boardId;
        }


        if (!empty($title)) {
            $requestOptions['title'] = $title;
        }

        if ($sectionId !== null) {
            $requestOptions['board_section_id'] = $sectionId;
        }

        return $this->post(UrlBuilder::RESOURCE_UPDATE_PIN, $requestOptions);
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

        $this->post(UrlBuilder::RESOURCE_REPIN, $requestOptions);

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

        return $this->get(UrlBuilder::RESOURCE_PIN_INFO, $requestOptions);
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

        return $this->paginate(UrlBuilder::RESOURCE_DOMAIN_FEED, $data, $limit);
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

        if ($aggregatedPinId === null) {
            return new Pagination();
        }

        $additionalData['aggregated_pin_data_id'] = $aggregatedPinId;

        return $this->paginate(UrlBuilder::RESOURCE_ACTIVITY, $additionalData, $limit);
    }

    /**
     * Get pins from user's feed
     *
     * @param int $limit
     * @return Pagination
     */
    public function feed($limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(UrlBuilder::RESOURCE_USER_FEED, [], $limit);
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

        return $this->paginate(UrlBuilder::RESOURCE_RELATED_PINS, $requestData, $limit);
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
     * @param int $limit
     * @return Pagination
     */
    public function visualSimilar($pinId, $limit = Pagination::DEFAULT_LIMIT)
    {
        $data = [
            'pin_id'          => $pinId,
            // Some magic numbers, I have no idea about them
            'crop'            => [
                'x'                => 0.16,
                'y'                => 0.16,
                'w'                => 0.66,
                'h'                => 0.66,
                'num_crop_actions' => 1,
            ],
            'force_refresh'   => true,
            'keep_duplicates' => false,
        ];

        return $this->paginate(UrlBuilder::RESOURCE_VISUAL_SIMILAR_PINS, $data, $limit);
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
        if (!isset($pinInfo['images']['orig']['url'])) {
            return false;
        }

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
            'aux_fields' => [],
            'prepend'    => false,
            'offset'     => 180,
            'section_id' => $topicId,
        ];

        return $this->paginate(UrlBuilder::RESOURCE_EXPLORE_PINS, $data, $limit);
    }

    /**
     * Get pin analytics, like numbers of clicks, views and repins
     * @param $pinId
     * @return array|bool|Response
     */
    public function analytics($pinId)
    {
        // Pinterest requires pinId to be a string
        $pinId = (string)$pinId;

        return $this->get(UrlBuilder::RESOURCE_PIN_ANALYTICS, ['pin_id' => $pinId]);
    }

    /**
     * @param string $pinId
     * @return int|null
     */
    protected function getAggregatedPinId($pinId)
    {
        $pinInfo = $this->info($pinId);

        return $pinInfo['aggregated_pin_data']['id'] ?? null;
    }

    /**
     * @param string|array $pinIds
     * @param int $boardId
     * @param string $editUrl
     * @return bool
     */
    protected function bulkEdit($pinIds, $boardId, $editUrl)
    {
        $data = [
            'board_id' => $boardId,
            'pin_ids'  => (array)$pinIds,
        ];

        return $this->post($editUrl, $data);
    }
}
