<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

/**
 * Trait UploadsImages
 *
 * @property Request $request
 * @property Response $response
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
        $result = $this->request->upload($image, UrlBuilder::IMAGE_UPLOAD);

        $this->response->fillFromJson($result);

        return $this->response->hasData('success') ?
            $this->response->getData('image_url') :
            null;
    }

    /**
     * @param string $url
     * @param string $postString
     * @return $this
     */
    abstract protected function execute($url, $postString = "");
}