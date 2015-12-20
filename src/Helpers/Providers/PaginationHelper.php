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
     * @param array  $params
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public function getPaginatedData($callback, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {
            if (self::reachBatchesLimit($batchesLimit, $batchesNum))  break;

            $items = [];
            $res = call_user_func_array($callback, $params);

            if (self::_responseHasData($res)) {
                $res = self::_clearResponseFromMetaData($res);
                $items = $res['data'];
            }

            if (empty($items)) return;

            if (isset($res['bookmarks'])) {
                $params['bookmarks'] = $res['bookmarks'];
            }

            $batchesNum++;
            yield $items;
        } while (self::_responseHasData($res));
    }

    /**
     * @param array $res
     * @return bool
     */
    protected function _responseHasData($res)
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
    protected function _clearResponseFromMetaData($res)
    {
        if (isset($res['data'][0]['type']) && $res['data'][0]['type'] == 'module') {
            array_shift($res['data']);
            return $res;
        }
        return $res;
    }
}
