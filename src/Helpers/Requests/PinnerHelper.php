<?php

namespace seregazhuk\PinterestBot\Helpers\Requests;

use seregazhuk\PinterestBot\Api\Request;

class PinnerHelper
{
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
        $dataJson = [
            "options" => [
                "username" => $username,
            ]
        ];

        return Request::createRequestData($dataJson, $sourceUrl, $bookmarks);
    }

    /**
     * Parses Pinterest API response with pinner name
     *
     * @param $res
     * @return null
     */
    public static function parseAccountNameResponse($res)
    {
        if (isset($res['resource_data'][1]['resource']['options']['username'])) {
            return $res['resource_data'][1]['resource']['options']['username'];
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
        ];
        return Request::createRequestData($dataJson, "/login/");
    }
}
