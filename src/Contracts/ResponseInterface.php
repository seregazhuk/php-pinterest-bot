<?php

namespace seregazhuk\PinterestBot\Contracts;

interface ResponseInterface
{
    /**
     * Check if specified data exists in response.
     *
     * @param array|null $response
     * @param null       $key
     *
     * @return array|bool
     */
    public function getData($response, $key = null);

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
     *
     * @return bool
     */
    public function hasErrors($response);

    /**
     * Checks if response is not empty.
     *
     * @param array $res
     *
     * @return bool
     */
    public function isEmpty($res);

    /**
     * Parse bookmarks from response.
     *
     * @param array $response
     *
     * @return array|null
     */
    public function getBookmarks($response);

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @param array $res
     *
     * @return array
     */
    public function getPaginationData($res);

    /**
     * Parses Pinterest search API response for data and bookmarks
     * for next pagination page.
     *
     * @param array $response
     * @param bool  $bookmarksUsed
     *
     * @return array|null
     */
    public function parseSearchWithBookmarks($response, $bookmarksUsed = true);

    /**
     * Returns last error in response.
     *
     * @return array
     */
    public function getLastError();
}
