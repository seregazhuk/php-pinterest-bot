<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

class Pagination
{
    /**
     * @var Provider
     */
    protected $provider;

    /**
     * @var array
     */
    protected $bookmarks = [];

    /**
     * @param Provider $provider
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Iterate through results of Api function call. By
     * default generator will return all pagination results.
     * To limit result batches, set $limit. Call function
     * of object to get data.
     *
     * @param string $method
     * @param array $params
     * @param int $limit
     * @return \Generator
     */
    public function paginateOver($method, $params, $limit = 0)
    {
        $resultsNum = 0;
        while (true) {
            $response = $this->callProviderRequest($method, $params);
            $results = $this->processProviderResponse($response);

            if (empty($results)) return;

            foreach ($results as $result) {
                $resultsNum++;
                yield $result;

                if ($this->reachesLimit($limit, $resultsNum) || $this->checkEndBookMarks()) {
                    return;
                }
            }
        }

        return;
    }

    /**
     * @param string $method
     * @param array $params
     * @return PaginatedResponse
     */
    protected function callProviderRequest($method, array $params)
    {
        $params['bookmarks'] = $this->bookmarks;

        return call_user_func_array([$this->provider, $method], $params);
    }

    /**
     * @param PaginatedResponse $response
     * @return array
     */
    protected function processProviderResponse(PaginatedResponse $response)
    {
        if ($response->hasResponseData()) {
            $this->bookmarks = $response->getBookmarks();

            return $response->getResponseData();
        }

        return [];
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
     * Remove 'module' data from response.
     *
     * @param array $response
     *
     * @return array mixed
     */
    protected function clearResponseFromMetaData($response)
    {
        if (isset($response['data'][0]['type']) && $response['data'][0]['type'] == 'module') {
            array_shift($response['data']);
        }

        return $response;
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
