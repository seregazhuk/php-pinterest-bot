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
        $result = $this->request
            ->upload($image, UrlBuilder::IMAGE_UPLOAD);

        $this->processResult($result);

        return $this->response->hasData('success') ?
            $this->response->getData('image_url') :
            null;
    }

    /**
     * @param string $res
     * @return Response
     */
    abstract protected function processResult($res);
}