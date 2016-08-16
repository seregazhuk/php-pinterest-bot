<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

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
     * @throws InvalidRequestException
     */
    public function upload($image)
    {
        $res = $this
            ->getRequest()
            ->upload($image, UrlHelper::IMAGE_UPLOAD);

        return $res['success'] ? $res['image_url'] : null;
    }
}