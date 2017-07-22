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
     * @return array|bool
     */
    public function create($pinId, $text)
    {
        $requestOptions = [
            'pin_id' => $pinId,
            'text'   => $text,
        ];

        return $this->post($requestOptions, UrlBuilder::RESOURCE_COMMENT_PIN);
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
        $requestOptions = [
            'pin_id'     => $pinId,
            'comment_id' => $commentId,
        ];

        return $this->post($requestOptions, UrlBuilder::RESOURCE_COMMENT_DELETE_PIN);
    }
}