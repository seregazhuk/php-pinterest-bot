<?php

namespace seregazhuk\PinterestBot\Api\Providers;

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
            'id'            => $pinId,
            'field_set_key' => 'detailed',
        ];

        $pinInfo = $this->get(UrlBuilder::RESOURCE_PIN_INFO, $requestOptions);

        $aggregatedPinId = isset($pinInfo['aggregated_pin_data']['id']) ? $pinInfo['aggregated_pin_data']['id'] : null;

        $requestOptions = [
            'objectId' => $aggregatedPinId,
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
}
