<?php

namespace seregazhuk\PinterestBot\Helpers;

use Traversable;
use IteratorAggregate;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

/**
 * Class Pagination
 * Iterate through results of Pinterest Api. By default iterator will return 50 first
 * pagination results. To change this behaviour specify another limit as the
 * constructor param. For no limits specify zero.
 *
 * @package seregazhuk\PinterestBot\Helpers
 */
class Pagination implements IteratorAggregate
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
     * @var int
     */
    protected $offset;

    /**
     * @param int $limit
     */
    public function __construct($limit = self::DEFAULT_LIMIT)
    {
        $this->limit = $limit;
    }

    /**
     * Sets a callback to make requests. Should be a closure
     * that accepts a $bookmarks array as an argument.
     *
     * @param callable $callback
     * @return $this
     */
    public function paginateOver(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Syntax sugar for getIterator method
     * @return Traversable
     */
    public function get()
    {
        return $this->getIterator();
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        $resultsNum = 0;
        $processed = 0;

        while (true) {
            $results = $this->getCurrentResults();

            if (empty($results)) return;

            foreach ($results as $result) {
                $processed++;

                if($processed > $this->offset) {
                    yield $result;
                    $resultsNum++;
                }

                if ($this->reachesLimit($resultsNum)) return;
            }

            if (empty($this->bookmarks)) return;
        }
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function skip($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function take($limit)
    {
        $this->limit = $limit;

        return $this;
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

        /** @var Response $response */
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
}
