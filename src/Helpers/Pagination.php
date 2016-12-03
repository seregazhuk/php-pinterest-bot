<?php

namespace seregazhuk\PinterestBot\Helpers;

use Traversable;
use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

/**
 * Class Pagination
 * Iterate through results of Api function call. By
 * default generator will return all pagination results.
 * To limit results, set $limit.
 *
 * @package seregazhuk\PinterestBot\Helpers
 */
class Pagination implements \IteratorAggregate
{
    const DEFAULT_LIMIT = 50;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var array
     */
    protected $bookmarks = [];

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param int $limit
     */
    public function __construct($limit = 0)
    {
        $this->limit = $limit;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function paginateOver(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        $resultsNum = 0;

        while (true) {
            $results = $this->getCurrentResults();

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
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->getIterator());
    }

    /**
     * @return array
     */
    protected function getCurrentResults()
    {
        $callback = $this->callback;

        $response = $callback($this->bookmarks);

        return $this->processResponse($response);
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
        return $this->reachesLimit($resultsNum) || $this->checkEndBookMarks();
    }

    /**
     * Check if we get results limit in pagination.
     *
     * @param int $resultsNum
     *
     * @return bool
     */
    protected function reachesLimit($resultsNum)
    {
        return $this->limit && $resultsNum >= $this->limit;
    }


    /**
     * Checks for -end- substring in bookmarks. This is pinterest sign of
     * the finished pagination.
     *
     * @return bool
     */
    protected function checkEndBookMarks()
    {
        return !empty($this->bookmarks) && $this->bookmarks[0] == '-end-';
    }
}
