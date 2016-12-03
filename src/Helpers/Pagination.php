<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

class Pagination
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $bookmarks = [];

    /**
     * @param int $limit
     */
    public function __construct($limit = 0)
    {
        $this->limit = $limit;
    }

    /**
     * Iterate through results of Api function call. By
     * default generator will return all pagination results.
     * To limit result batches, set $limit. Call function
     * of object to get data.
     *
     * @param callable $callback
     * @return \Generator
     */
    public function paginateOver(callable $callback)
    {
        $resultsNum = 0;
        while (true) {

            $response = $callback($this->bookmarks);
            $results = $this->processResponse($response);

            if (empty($results)) return;

            foreach ($results as $result) {
                $resultsNum++;
                yield $result;

                if ($this->paginationFinished($resultsNum)) {
                    return;
                }
            }
        }

        return;
    }

    /**
     * @param PaginatedResponse $response
     * @return array
     */
    protected function processResponse(PaginatedResponse $response)
    {
        if ($response->isEmpty()) return [];

        $this->bookmarks = $response->getBookmarks();

        return $response->getResponseData();
    }

    /**
     * @param int $resultsNum
     * @return bool
     */
    protected function paginationFinished($resultsNum)
    {
        return $this->reachesLimit($this->limit, $resultsNum) || $this->checkEndBookMarks();
    }

    /**
     * Check if we get batches limit in pagination.
     *
     * @param int $limit
     * @param int $resultsNum
     *
     * @return bool
     */
    protected function reachesLimit($limit, $resultsNum)
    {
        return $limit && $resultsNum >= $limit;
    }


    /**
     * Checks for -end- substring in bookmarks
     *
     * @return bool
     */
    protected function checkEndBookMarks()
    {
        return !empty($this->bookmarks) && $this->bookmarks[0] == '-end-';
    }
}
