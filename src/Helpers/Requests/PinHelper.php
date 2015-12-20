<?php

namespace seregazhuk\PinterestBot\Helpers\Requests;

use seregazhuk\PinterestBot\Api\Request;

class PinHelper
{
    /**
     * Create Pinterest API request form commenting/deleting comment pin
     *
     * @param int   $pinId
     * @param array $data
     * @return array
     */
    public static function createCommentRequest($pinId, $data)
    {
        $dataJson = self::createPinIdRequest($pinId, $data);

        return self::createPinRequestData($dataJson);
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
        ];

        return self::createPinRequestData($dataJson, "/pin/create/bookmarklet/?url=".urlencode($imageUrl));
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

        return self::createPinRequestData($dataJson);
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
                "field_set_key" => "detailed",
                "id"            => $pinId,
                "pin_id"        => $pinId,
                "allow_stale"   => true,
            ],
        ];

        return self::createPinRequestData($dataJson);
    }

    /**
     * Creates common pin request data by PinId
     *
     * @param int    $pinId
     * @param string $template
     * @param array  $options
     * @return array
     */
    public static function createPinRequest($pinId, $template = 'id', $options = array())
    {
        $options = array_merge(
            ["$template" => $pinId], $options
        );

        $result = [
            "options" => $options,
            "context" => [],
        ];

        return $result;
    }

    /**
     * Creates simple Pin request by PinId (used by delete and like requests)
     *
     * @param int $pinId
     * @return array
     */
    public static function createSimplePinRequest($pinId)
    {
        $dataJson = self::createPinRequest($pinId);

        return self::createPinRequestData($dataJson);
    }

    /**
     * @param string|null $sourceUrl
     * @param array       $data
     * @return array
     */
    public static function createPinRequestData($data, $sourceUrl = null)
    {
        if ($sourceUrl === null) {
            reset($data);
            $sourceUrl = "/pin/".end($data["options"])."/";
        }

        return Request::createRequestData($data, $sourceUrl);
    }

    /**
     * @param       $pinId
     * @param array $options
     * @return array
     */
    public static function createPinIdRequest($pinId, $options = array())
    {
        return self::createPinRequest($pinId, 'pin_id', $options);
    }
}
