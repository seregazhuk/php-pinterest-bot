<?php


namespace szhuk\PinterestAPI\helpers;


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
}