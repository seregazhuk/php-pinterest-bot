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
*@param mixed  $obj
     * @param string $function
     * @param array  $params
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public static function getPaginatedData($obj, $function, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {
            if ($batchesLimit && $batchesNum >= $batchesLimit) {
                break;
            }

            $items = [];
            $res   = call_user_func_array([$obj, $function], $params);

            if (isset($res['data']) && ! empty($res['data'])) {
                if (isset($res['data'][0]['type']) && $res['data'][0]['type'] == 'module') {
                    array_shift($res['data']);
                }
                $items = $res['data'];
            }

            if (isset($res['bookmarks'])) {
                $params['bookmarks'] = $res['bookmarks'];
            }

            if (empty($items)) {
                return;
            }

            $batchesNum++;
            yield $items;


        } while (isset($res['data']) && ! empty($res['data']));

    }
}
