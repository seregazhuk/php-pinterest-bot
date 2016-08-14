<?php

namespace seregazhuk\PinterestBot\Helpers;

use seregazhuk\PinterestBot\Api\Providers\Provider;

class Pagination
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var array
     */
    protected $bookmarks = [];

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
     * @return \Iterator
     */
    public function paginateOver($method, $params, $limit = 0)
    {
        $resultsNum = 0;
        while (true) {
            
            $results = $this->callProviderRequest($method, $params);
            if (empty($results) || $this->checkEndBookMarks()) {
                return;
            }

            foreach ($results as $result) {
                $resultsNum++;
                yield $result;

                if ($this->reachesLimit($limit, $resultsNum)) {
                    return;
                }
            }
        }
    }

    /**
     * @param string $method
     * @param array $params
     * @return array
     */
    protected function callProviderRequest($method, array $params)
    {
        $params['bookmarks'] = $this->bookmarks;

        $response = call_user_func_array([$this->provider, $method], $params);
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
     * @return bool
     */
    protected function checkEndBookMarks()
    {
        return !empty($this->bookmarks) && $this->bookmarks[0] == '-end-';
    }
}
