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

    /**
     * Check if specified data exists in response
     * @param      $response
     * @param null $key
     * @return array|bool
     */
    public static function getDataFromResponse($response, $key = null)
    {
        if (isset($response['resource_response']['data'])) {
            $data = $response['resource_response']['data'];

            if ($key) {
                return array_key_exists($key, $data) ? $data[$key] : false;
            }
            return $data;
        }
        return false;
    }
}