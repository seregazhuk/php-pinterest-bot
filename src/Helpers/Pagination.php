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
     * To limit result batches, set $batchesLimit. Call function
     * of object to get data.
     *
     * @param string $method
     * @param array $params
     * @param int $batchesLimit
     * @return \Iterator
     */
    public function paginate($method, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {
            $results = $this->callProviderRequest($method, $params);
            if (empty($results) || $this->checkEndBookMarks()) {
                return;
            }

            $batchesNum++;
            foreach ($results as $result) {
                yield $result;
            }

        } while (!$this->reachBatchesLimit($batchesLimit, $batchesNum));
    }

    protected function callProviderRequest($method, array $params)
    {
        $params['bookmarks'] = $this->bookmarks;
        $response = call_user_func_array([$this->provider, $method], $params);

        if ($this->responseHasData($response)) {
            $this->getBookMarks($response);

            return $this->getDataFromPaginatedResponse($response);
        }

        return [];
    }

    protected function getDataFromPaginatedResponse($response)
    {
        if ($this->responseHasData($response)) {
            $res = $this->clearResponseFromMetaData($response);

            return $res['data'];
        }

        return [];
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    protected function responseHasData($response)
    {
        return isset($response['data']) && !empty($response['data']);
    }

    /**
     * Check if we get batches limit in pagination.
     *
     * @param int $batchesLimit
     * @param int $batchesNum
     *
     * @return bool
     */
    protected function reachBatchesLimit($batchesLimit, $batchesNum)
    {
        return $batchesLimit && $batchesNum >= $batchesLimit;
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
     * @param $response
     *
     * @return array
     */
    protected function getBookMarks($response)
    {
        $this->bookmarks = isset($response['bookmarks']) ? $response['bookmarks'] : [];

        return $this;
    }

    protected function checkEndBookMarks()
    {
        return !empty($this->bookmarks) && $this->bookmarks[0] == '-end-';
    }
}
