<?php

namespace seregazhuk\PinterestBot\Contracts;

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
     * @return array
     */
    public function getResponseData();
}