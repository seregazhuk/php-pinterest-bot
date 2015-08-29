<?php

namespace seregazhuk\PinterestBot\Helpers;


class PinHelper
{
    /**
     * Creates Pinterest API request for like/unlike pin
     *
     * @param int $pinId
     * @return array
     */
    public static function createLikeRequest($pinId)
    {
        $dataJson = [
            "options" => [
                "pin_id" => $pinId,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Create Pinterest API request form commenting pin
     *
     * @param int    $pinId
     * @param string $text
     * @return array
     */
    public static function createCommentRequest($pinId, $text)
    {
        $dataJson = [
            "options" => [
                "pin_id" => $pinId,
                "text"   => $text,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Create Pinterest API request form commenting pin
     *
     * @param int $pinId
     * @param int $commentId
     * @return array
     */
    public static function createCommentDeleteRequest($pinId, $commentId)
    {
        $dataJson = [
            "options" => [
                "pin_id"     => $pinId,
                "comment_id" => $commentId,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
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
        } else {
            return false;
        }
    }

    /**
     * Creates Pinterest API request for Pin creation
     *
     * @param string $description
     * @param string $imageUrl
     * @param int    $boardId
     * @return array
     */
    public static function createPinCreationRequest($imageUrl, $boardId, $description = "")
    {
        $dataJson = [
            "options" => [
                "method"      => "scraped",
                "description" => $description,
                "link"        => $imageUrl,
                "image_url"   => $imageUrl,
                "board_id"    => $boardId,
            ],
            "context" => new \stdClass(),
        ];

        return [
            "source_url" => "/pin/create/bookmarklet/?url=" . urlencode($imageUrl),
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }


    /**
     * Creates Pinterest API request for Pin repin
     *
     * @param string $description
     * @param int    $repinId
     * @param int    $boardId
     * @return array
     */
    public static function createRepinRequest($repinId, $boardId, $description)
    {
        $dataJson = [
            "options" => [
                "board_id"    => $boardId,
                "description" => stripslashes($description),
                "link"        => stripslashes($repinId),
                "is_video"    => null,
                "pin_id"      => $repinId,
            ],
            "context" => [],
        ];

        return [
            "source_url" => "/pin/{$repinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }


    /**
     * Parses pin create response
     *
     * @param $response
     * @return bool
     */
    public static function parsePinCreateResponse($response)
    {
        if (isset($response['resource_response']['data']['id'])) {
            return $response['resource_response']['data']['id'];
        }

        return false;
    }

    /**
     * Creates Pinterest API request to delete pin by its ID
     *
     * @param int $pinId
     * @return array
     */
    public static function createDeleteRequest($pinId)
    {

        $dataJson = [
            "options" => [
                "id" => $pinId,
            ],
            "context" => new \stdClass(),
        ];

        return [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Creates Pinterest API request to get Pin info
     *
     * @param int $pinId
     * @return array
     */
    public static function createInfoRequest($pinId)
    {
        $dataJson = [

            "options" => [
                "field_set_key"               => "detailed",
                "fetch_visual_search_objects" => true,
                "id"                          => $pinId,
                "allow_stale"                 => true,
            ],
            "context" => new \StdClass(),
        ];

        return [
            "source_url" => "/pin/$pinId/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }

    /**
     * Parses Pinterest API response with pin information
     *
     * @param array $res
     * @return null|array
     */
    public static function parsePinInfoResponse($res)
    {
        if ($res) {
            if (isset($res['resource_response']['data'])) {
                return $res['resource_response']['data'];
            }
        }

        return null;
    }
}