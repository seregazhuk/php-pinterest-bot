<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait UploadsImages
 */
trait UploadsImages
{
    use HandlesRequest;

    /**
     * @param string $image
     * @return string|null
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
