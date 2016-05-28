<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

trait UploadsImages
{
    use ProviderTrait;

    public function upload($image)
    {
        $res = $this->getRequest()->upload($image, UrlHelper::IMAGE_UPLOAD);

        return $res['success'] ? $res['image_url'] : null;
    }
}