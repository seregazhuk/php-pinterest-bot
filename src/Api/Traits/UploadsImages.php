<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

trait UploadsImages
{
    use HandlesRequestAndResponse;
    
    public function upload($image)
    {
        $res = $this->getRequest()->upload($image, UrlHelper::IMAGE_UPLOAD);

        return $res['success'] ? $res['image_url'] : null;
    }
}