<?php

namespace seregazhuk\PinterestBot\Helpers;

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
        $dataJson = self::createPinnerRequestData($username);

        if ( ! empty($bookmarks)) {
            $dataJson["options"]["bookmarks"] = $bookmarks;
        }

        return parent::createRequestData($dataJson, $sourceUrl);

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
        $dataJson                        = self::createPinnerRequestData($username);
        $dataJson["options"]["password"] = $password;

        return parent::createRequestData($dataJson, "/login/");
    }

    /**
     * Parses Pintrest Api response after login
     *
     * @param              $res
     * @return bool
     */
    public static function parseLoginResponse($res)
    {
        if ($res === null) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Creates common Pinner request data by username
     *
     * @param $username
     * @return array
     */
    protected static function createPinnerRequestData($username)
    {
        return [
            "options" => [
                "username_or_email" => $username,
            ],
            "context" => new \stdClass(),
        ];
    }
}
