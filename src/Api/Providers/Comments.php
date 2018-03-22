<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

class Comments extends Provider
{
    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'delete',
        'create',
    ];

    /**
     * Write a comment for a pin with current id.
     *
     * @param int $pinId
     * @param string $text Comment
     *
     * @return array
     */
    public function create($pinId, $text)
    {
        $requestOptions = [
            'objectId' => $this->getAggregatedPinId($pinId),
            'pinId'    => $pinId,
            'text'     => $text,
        ];

        return $this->post(UrlBuilder::RESOURCE_COMMENT_PIN, $requestOptions, true);
    }

    /**
     * Delete a comment for a pin with current id.
     *
     * @param string $pinId
     * @param int $commentId
     *
     * @return bool
     */
    public function delete($pinId, $commentId)
    {
        $requestOptions = ['commentId' => $commentId];

        return $this->post(UrlBuilder::RESOURCE_COMMENT_DELETE_PIN, $requestOptions);
    }


    /**
     * @param string $pinId
     * @param int $limit
     * @return Pagination
     */
    public function getList($pinId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            'resource/AggregatedCommentResource/get/',
            ['bookmarks' => '', 'objectId' => $this->getAggregatedPinId($pinId), 'page_size' => 2],
            $limit
        );
    }

    /**
     * @param string $pinId
     * @return null|string
     */
    protected function getAggregatedPinId($pinId)
    {
        $requestOptions = [
            'id'            => $pinId,
            'field_set_key' => 'detailed',
        ];

        $pinInfo = $this->get(UrlBuilder::RESOURCE_PIN_INFO, $requestOptions);

        return isset($pinInfo['aggregated_pin_data']['id']) ? $pinInfo['aggregated_pin_data']['id'] : null;
    }
}
