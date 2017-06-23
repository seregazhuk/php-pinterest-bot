<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

/**
 * Trait UploadsImages
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
        $result = $this->getRequest()->upload($image, UrlBuilder::IMAGE_UPLOAD);

        $response = $this->getResponse();
        $response->fillFromJson($result);

        return $response->hasData('success') ?
            $response->getData('image_url') :
            null;
    }
}