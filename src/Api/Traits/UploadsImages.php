<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

/**
 * Trait UploadsImages
 * @package seregazhuk\PinterestBot\Api\Traits
 */
trait UploadsImages
{
    use HandlesRequest;

    /**
     * @param string $image
     * @return string|null
     * @throws InvalidRequest
     */
    public function upload($image)
    {
        $res = $this
            ->getRequest()
            ->upload($image, UrlBuilder::IMAGE_UPLOAD);

        return $res['success'] ? $res['image_url'] : null;
    }
}