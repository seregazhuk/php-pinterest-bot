<?php

namespace seregazhuk\PinterestBot\Helpers;

use Traversable;
use EmptyIterator;
use IteratorAggregate;
use seregazhuk\PinterestBot\Api\Response;

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
     * @var callable
     */
    protected $callback;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $resultsNum;

    /**
     * @var int
     */
    protected $processed;

    /**
     * @param int $limit
     */
    public function __construct($limit = self::DEFAULT_LIMIT)
    {
        $this->limit = $limit;
    }

    /**
     * Sets a callback to make requests. Should be a closure.
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
        if(empty($this->callback)) return new EmptyIterator();

        return $this->processCallback();
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
     * Check if we execGet results limit in pagination.
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
     * @return \Generator|void
     */
    protected function processCallback()
    {
        $this->resultsNum = 0;
        $this->processed = 0;
        $callback = $this->callback;

        while (true) {
            /** @var Response $response */
            $response = $callback();

            if ($response->isEmpty()) return;

            foreach ($response->getResponseData() as $result) {
                $this->processed++;

                if ($this->processed > $this->offset) {
                    yield $result;
                    $this->resultsNum++;
                }

                if ($this->reachesLimit($this->resultsNum)) return;
            }

            if (!$response->hasBookmarks()) return;
        }
    }
}
