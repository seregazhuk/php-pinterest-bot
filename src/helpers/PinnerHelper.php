<?php


namespace szhuk\PinterestAPI\helpers;


use szhuk\PinterestAPI\ApiInterface;

class PinnerHelper
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
        } else {

            if (isset($res['resource']['options']['bookmarks'][0])) {
                $bookmarks = [$res['resource']['options']['bookmarks'][0]];
            } else {
                $bookmarks = null;
            }

            if (isset($res['resource_response']['data'])) {
                $data = $res['resource_response']['data'];

                return ['data' => $data, 'bookmarks' => $bookmarks];
            } else {
                return [];
            }
        }
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
        $dataJson = [
            "options" => [
                "username" => $username,
            ],
            "context" => new \stdClass(),
        ];

        if ( ! empty($bookmarks)) {
            $dataJson["options"]["bookmarks"] = $bookmarks;
        }

        return [
            "source_url" => $sourceUrl,
            "data"       => json_encode($dataJson, true),
        ];

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
                "password"          => $password,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/login/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Parses Pintrest Api response after login
     *
     * @param              $res
     * @param ApiInterface $apiInterface
     * @return bool
     */
    public static function parseLoginResponse($res, ApiInterface $apiInterface)
    {
        if ($res === null) {
            return false;
        } else {
            $apiInterface->setLoggedIn(CsrfHelper::getCsrfToken($apiInterface->getCookieJar()));

            return true;
        }
    }
}