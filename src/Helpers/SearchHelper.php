<?php

namespace seregazhuk\PinterestBot\Helpers;

class SearchHelper
{
    /**
     * Creates Pinterest API search request
     *
     * @param       $query
     * @param       $scope
     * @param array $bookmarks
     * @return array
     */
    public static function createSearchRequest($query, $scope, $bookmarks = [])
    {
        $modulePath = 'App(module=[object Object])';
        $options = [
            "restrict"            => null,
            "scope"               => $scope,
            "constraint_string"   => null,
            "show_scope_selector" => true,
            "query"               => $query,
        ];
        $dataJson = [
            "options" => $options,
        ];

        if (empty($bookmarks)) {
            $dataJson = self::createSimpleSearchRequest($dataJson);
        } else {
            $dataJson = self::createBookMarkedSearchRequest($dataJson, $bookmarks);
        }

        return [
            "source_url"  => "/search/$scope/?q=" . $query,
            "data"        => json_encode($dataJson),
            "module_path" => urlencode($modulePath),
        ];
    }

    protected static function createSimpleSearchRequest($requestData)
    {
        $dataJson = [
            'module' => [
                "name"    => "SearchPage",
                "options" => $requestData['options'],
            ],
            "options" => $requestData['options'],
        ];
        return $dataJson;
    }

    protected static function createBookMarkedSearchRequest($requestData, $bookmarks)
    {
        $dataJson = [
            "options" => array_merge(
                $requestData['options'], [
                    "bookmarks" => $bookmarks,
                    "layout"    => null,
                    "places"    => false,
                ]
            ),
        ];

        return $dataJson;
    }

    /**
     * Parses Pinterest search API response for data and bookmarks
     * for next pagination page
     *
     * @param array $res
     * @param bool  $bookmarksUsed
     * @return array|null
     */
    public static function parseSearchResponse($res, $bookmarksUsed)
    {
        if ($res === null || ! $bookmarksUsed) return self::parseSimpledSearchResponse($res);

        return self::parseBookMarkedSearchResponse($res);
    }


    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks
     *
     * @param $res
     * @return array
     */
    public static function parseSimpledSearchResponse($res)
    {
        $bookmarks = [];

        if (isset($res['module']['tree']['resource']['options']['bookmarks'][0])) {
            $bookmarks = $res['module']['tree']['resource']['options']['bookmarks'][0];
        }

        if ( ! empty($res['module']['tree']['data']['results'])) {
            return ['data' => $res['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
        }

        return [];
    }

    /**
     * Parses Pinterest search API response
     * on request without bookmarks
     *
     * @param $res
     * @return array
     */
    public static function parseBookMarkedSearchResponse($res)
    {
        if ( ! empty($res['resource_response']['data'])) {
            $res = [
                'data'      => $res['resource_response']['data'],
                'bookmarks' => $res['resource']['options']['bookmarks'],
            ];
            return $res;
        }

        return [];
    }
}
