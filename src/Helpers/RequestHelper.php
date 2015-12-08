<?php

namespace seregazhuk\PinterestBot\Helpers;

class RequestHelper
{
    /**
     * @param string|null $sourceUrl
     * @param array       $data
     * @return array
     */
    public static function createRequestData($data, $sourceUrl = null)
    {
        return [
            "source_url" => $sourceUrl,
            "data"       => json_encode($data),
        ];
    }
}