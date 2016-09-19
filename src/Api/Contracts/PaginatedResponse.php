<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface PaginatedResponse
{
    /**
     * @return bool
     */
    public function hasResponseData();

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks();

    /**
     * @return array|bool
     */
    public function getResponseData();
}