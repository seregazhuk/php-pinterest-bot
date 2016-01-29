<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use Iterator;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

trait FollowersTrait
{
    use ProviderTrait;

    /**
     * @param array $data
     * @param string $url
     * @param string $sourceUrl
     * @param array $bookmarks
     * @return array
     */
    public function getData($data, $url, $sourceUrl, $bookmarks = [])
    {
        $data['options'] = $data;
        $get = Request::createRequestData($data, $sourceUrl, $bookmarks);
        $getString = UrlHelper::buildRequestString($get);
        $response = $this->getRequest()->exec($url.'?'.$getString);

        return $this->getResponse()->getPaginationData($response);
    }

    /**
     * @param array $data
     * @param string $resourceUrl
     * @param string $sourceUrl
     * @param int $batchesLimit
     * @return Iterator
     */
    public function getFollowData($data, $resourceUrl, $sourceUrl, $batchesLimit = 0)
    {
        $requestData = array_merge([$data, $resourceUrl, $sourceUrl]);

        return Pagination::getPaginatedData([$this, 'getData'], $requestData, $batchesLimit);
    }

}