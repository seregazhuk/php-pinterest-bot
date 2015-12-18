<?php

namespace seregazhuk\PinterestBot\Helpers\Providers;

class RequestHelper
{
    /**
     * @param array|object $data
     * @param string|null  $sourceUrl
     * @param array        $bookmarks
     * @return array
     */
    public static function createRequestData($data, $sourceUrl = null, $bookmarks = [])
    {
        if ( ! empty($bookmarks)) {
            $data["options"]["bookmarks"] = $bookmarks;
        }

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

    /**
     * Parse bookmarks from response
     * @param array $response
     * @return string|null
     */
    protected static function _getBookmarksFromResponse($response)
    {
        if (isset($response['resource']['options']['bookmarks'][0])) {
            return [$response['resource']['options']['bookmarks'][0]];
        }
        return null;
    }

    /**
     * Checks result of PIN-methods
     *
     * @param array $res
     * @return bool
     */
    public static function checkMethodCallResult($res)
    {
        if ($res !== null && isset($res['resource_response'])) {
            return true;
        }

        return false;
    }

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @param array $res
     * @return array
     */
    public static function parsePaginatedResponse($res)
    {
        if ( ! self::checkMethodCallResult($res)) {
            return [];
        }

        $bookmarks = self::_getBookmarksFromResponse($res);
        if ($data = self::getDataFromResponse($res)) {
            return ['data' => $data, 'bookmarks' => $bookmarks];
        }

        return [];
    }

}