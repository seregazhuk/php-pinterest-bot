<?php

namespace seregazhuk\PinterestBot\Helpers;

class PaginationHelper
{
    /**
     * Iterate through results of Api function call. By
     * default generator will return all pagination results.
     * To limit result batches, set $batchesLimit. Call function
     * of object to get data.
     *
     * @param mixed  $obj
     * @param string $function
     * @param array  $params
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public static function getPaginatedData($obj, $function, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {
            if (self::reachBatchesLimit($batchesLimit, $batchesNum))  break;

            $items = [];
            $res = call_user_func_array([$obj, $function], $params);

            if (self::_responseHasData($res)) {
                if (isset($res['data'][0]['type']) && $res['data'][0]['type'] == 'module') {
                    array_shift($res['data']);
                }
                $items = $res['data'];
            }

            if (isset($res['bookmarks'])) {
                $params['bookmarks'] = $res['bookmarks'];
            }

            if (empty($items)) return;

            $batchesNum++;
            yield $items;
        } while (self::_responseHasData($res));
    }

    /**
     * @param array $res
     * @return bool
     */
    protected static function _responseHasData($res)
    {
        return isset($res['data']) && ! empty($res['data']);
    }

    /**
     * Check if we get batches limit in pagination
     * @param int $batchesLimit
     * @param int $batchesNum
     * @return bool
     */
    protected static function reachBatchesLimit($batchesLimit, $batchesNum)
    {
        return $batchesLimit && $batchesNum >= $batchesLimit;
    }
}
