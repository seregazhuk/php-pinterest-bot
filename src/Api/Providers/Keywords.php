<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Keywords extends Provider
{
    /**
     * Get recommendations for query word. 
     * 
     * @param string $query
     * @return array|bool
     */
    public function recommendedFor($query)
    {
        $requestOptions = [
            'scope' => 'pins',
            'query' => $query,
        ];

        $result = $this->execGetRequest($requestOptions, UrlBuilder::getSearchUrl());

        return $this->getKeywordsFromRequest($result);
    }

    /**
     * @param bool|array $response
     * @return bool|array
     */
    protected function getKeywordsFromRequest($response)
    {
        $keywords = $response['guides'];

        if (empty($keywords)) return [];

        return array_map(function ($keywordData) {
            return [
                'term'     => $keywordData['term'],
                'display'  => $keywordData['display'],
                'position' => $keywordData['position'],
            ];
        }, $keywords);
    }
}