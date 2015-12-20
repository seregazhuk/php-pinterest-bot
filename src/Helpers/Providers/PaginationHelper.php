<?php

namespace seregazhuk\PinterestBot\Helpers\Providers;

trait PaginationHelper
{
    /**
     * Iterate through results of Api function call. By
     * default generator will return all pagination results.
     * To limit result batches, set $batchesLimit. Call function
     * of object to get data.
     *
     * @param callable $callback
     * @param array    $params
     * @param int      $batchesLimit
     * @return \Iterator
     */
    public function getPaginatedData($callback, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {
            $response = self::getPaginatedResponse($callback, $params);
            $items = self::getDataFromPaginatedResponse($response);

            if (empty($items)) {
                return;
            }

            $params['bookmarks'] = self::getBookMarks($response);
            $batchesNum++;
            yield $items;
        } while ( ! self::reachBatchesLimit($batchesLimit, $batchesNum));
    }

    protected function getPaginatedResponse(callable $callback, array $params)
    {
        $response = call_user_func_array($callback, $params);
        if (self::responseHasData($response)) {
            return self::clearResponseFromMetaData($response);
        }

        return [];
    }

    protected function getDataFromPaginatedResponse($response)
    {
        if (self::responseHasData($response)) {
            $res = self::clearResponseFromMetaData($response);

            return $res['data'];
        }

        return [];
    }

    /**
     * @param array $res
     * @return bool
     */
    protected function responseHasData($res)
    {
        return isset($res['data']) && ! empty($res['data']);
    }

    /**
     * Check if we get batches limit in pagination
     * @param int $batchesLimit
     * @param int $batchesNum
     * @return bool
     */
    protected function reachBatchesLimit($batchesLimit, $batchesNum)
    {
        return $batchesLimit && $batchesNum >= $batchesLimit;
    }

    /**
     * Remove 'module' data from response
     * @param array $res
     * @return array mixed
     */
    protected function clearResponseFromMetaData($res)
    {
        if (isset($res['data'][0]['type']) && $res['data'][0]['type'] == 'module') {
            array_shift($res['data']);
        }

        return $res;
    }

    /**
     * @param $response
     * @return array
     */
    protected function getBookMarks($response)
    {
        return isset($response['bookmarks']) ? $response['bookmarks'] : [];
    }
}
