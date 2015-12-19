<?php

namespace seregazhuk\PinterestBot\Helpers;

class SearchHelper extends RequestHelper
{
    const MODULE_SEARCH_PAGE = "SearchPage";

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
        $options = [
            "scope" => $scope,
            "query" => $query,
        ];

        $dataJson = ["options" => $options];

        if ( ! empty($bookmarks)) {
            $dataJson['options']['bookmarks'] = $bookmarks;
        } else {
            $dataJson = array_merge(
                $dataJson, [
                'module' => [
                    "name"    => self::MODULE_SEARCH_PAGE,
                    "options" => $options,
                ],
            ]
            );
        }

        return self::createRequestData(
            $dataJson, "/search/$scope/?q=" . $query
        );
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
        if ($res === null || ! $bookmarksUsed) {
            return self::parseSimpledSearchResponse($res);
        }

        return self::parsePaginatedResponse($res);
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
}
