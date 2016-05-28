<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use Iterator;
use seregazhuk\PinterestBot\Helpers\Pagination;

trait HasFollowers
{
    use ProviderTrait;

    /**
     * @param array  $data
     * @param string $resourceUrl
     * @param string $sourceUrl
     * @param int    $batchesLimit
     *
     * @return Iterator
     */
    public function getFollowData($data, $resourceUrl, $sourceUrl, $batchesLimit = 0)
    {
        $requestData = array_merge([$data, $resourceUrl, $sourceUrl]);

        return (new Pagination($this))->getPaginatedData('getPaginatedData', $requestData, $batchesLimit);
    }
}
