<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Exceptions\AuthException;

class PinnerHelper extends RequestHelper
{
    /**
     * Checks Pinterest API pinners response, and parses data
     * with bookmarks info from it.
     *
     * @param array $res
     * @return array
     */
    public static function checkUserDataResponse($res)
    {
        if ($res === null) {
            return [];
        }

        $bookmarks = self::_getBookmarksFromResponse($res);
        if ($data = self::_getDataFromResponse($res)) {
            return ['data' => $data, 'bookmarks' => $bookmarks];
        }

        return [];
    }

    /**
     * Creates Pinterest API request to get user info according to
     * username, API url and bookmarks for pagination
     *
     * @param string $username
     * @param string $sourceUrl
     * @param array  $bookmarks
     * @return array
     */
    public static function createUserDataRequest($username, $sourceUrl, $bookmarks)
    {
        $dataJson = self::createPinnerRequestData($username);

        if ( ! empty($bookmarks)) {
            $dataJson["options"]["bookmarks"] = $bookmarks;
        }

        return self::createRequestData($dataJson, $sourceUrl);

    }

    /**
     * Parses Pinterest API response with pinner name
     *
     * @param $res
     * @return null
     */
    public static function parseAccountNameResponse($res)
    {
        if (isset($res['resource_data_cache'][1]['resource']['options']['username'])) {
            return $res['resource_data_cache'][1]['resource']['options']['username'];
        }

        return null;
    }

    /**
     * Creates Pinterest API request to login
     *
     * @param string $username
     * @param string $password
     * @return array
     */
    public static function createLoginRequest($username, $password)
    {
        $dataJson = [
            "options" => [
                "username_or_email" => $username,
                "password"          => $password
            ],
            "context" => new \stdClass(),
        ];
        return self::createRequestData($dataJson, "/login/");
    }

    /**
     * Parses Pintrest Api response after login
     *
     * @param array $res
     * @return bool
     * @throws AuthException
     */
    public static function parseLoginResponse($res)
    {
        if (isset($res['resource_response']['error'])) {
            throw new AuthException($res['resource_response']['error']['message']);
        }

        return true;
    }

    /**
     * Creates common Pinner request data by username
     *
     * @param string $username
     * @return array
     */
    protected static function createPinnerRequestData($username)
    {
        return [
            "options" => [
                "username" => $username,
            ],
            "context" => new \stdClass(),
        ];
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
     * Parse data from response
     * @param $response
     * @return array|null
     */
    protected static function _getDataFromResponse($response)
    {
        if (isset($response['resource_response']['data'])) {
            return $response['resource_response']['data'];
        }
        return null;
    }
}
