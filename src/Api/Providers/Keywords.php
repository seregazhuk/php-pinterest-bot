<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Keywords extends Provider
{
    public function recommendedFor($query)
    {
        $data = ['options' => ['scope' => 'pins', 'query' => $query]];
        $get = Request::createQuery($data);
        $response = $this->getRequest()->exec(UrlHelper::getSearchUrl() . '?' . $get);

        return $this->parseKeywordsFromRequest($response);
    }

    /**
     * @param array $response
     * @return array|null
     */
    protected function parseKeywordsFromRequest($response)
    {
        if (!isset($response['resource_data_cache'][0]['data']['guides'])) {
            return null;
        }

        $keywords = $response['resource_data_cache'][0]['data']['guides'];

        return array_map(
            function ($keywordData) {
                return [
                    'term'     => $keywordData['term'],
                    'position' => $keywordData['position'],
                    'display'  => $keywordData['display']
                ];
            }, $keywords
        );
    }
}