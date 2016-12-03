<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface PaginatedResponse
{
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

    /**
     * @return bool
     */
    public function isEmpty();
}