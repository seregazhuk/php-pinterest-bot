<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Keywords extends Provider
{
    /**
     * Get recommendations for query word. 
     * 
     * @param $query
     * @return array|bool
     */
    public function recommendedFor($query)
    {
        $requestOptions = ['scope' => 'pins', 'query' => $query];

        $result = $this->execGetRequest($requestOptions, UrlBuilder::getSearchUrl());

        return $this->parseKeywordsFromRequest($result);
    }

    /**
     * @param bool|array $response
     * @return bool|array
     */
    protected function parseKeywordsFromRequest($response)
    {
        if (empty($response) || !isset($response['guides'])) {
            return [];
        }

        $keywords = $response['guides'];

        return array_map(function ($keywordData) {
            return [
                'term'     => $keywordData['term'],
                'display'  => $keywordData['display'],
                'position' => $keywordData['position'],
            ];
        }, $keywords);
    }
}