<?php

namespace seregazhuk\PinterestBot\Helpers;

class RequestHelper
{
    /**
     * @param array|object $data
     * @param string|null  $sourceUrl
     * @param array        $bookmarks
     * @return array
     */
    public static function createRequestData($data = [], $sourceUrl = '/', $bookmarks = [])
    {
        if (empty($data)) {
            $data = self::createEmptyRequestData();
        }

        if ( ! empty($bookmarks)) {
            $data["options"]["bookmarks"] = $bookmarks;
        }

        $data["context"] = new \stdClass();

        return [
            "source_url" => $sourceUrl,
            "data"       => json_encode($data),
        ];
    }


    /**
     * @return array
     */
    protected static function createEmptyRequestData()
    {
        return array('options' => []);
    }

}