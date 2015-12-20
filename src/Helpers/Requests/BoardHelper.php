<?php

namespace seregazhuk\PinterestBot\Helpers\Requests;

class BoardHelper
{
    /**
     * Creates Pinterest API request to get boards info
     *
     * @return array
     */
    public static function createBoardsInfoRequest()
    {
        $dataJson = [
            "options" => [
                "filter"        => "all",
                "field_set_key" => "board_picker",
            ],
            "context" => [],
        ];

        return [
            "source_url"  => "/pin/create/bookmarklet/?url=",
            "pinFave"     => "1",
            "description" => "",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
    }
}
