<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

class Keywords extends Provider
{
    public function recommendedFor($query)
    {
        $result = $this->execGetRequest(['scope' => 'pins', 'query' => $query], UrlHelper::getSearchUrl());

        return $this->parseKeywordsFromRequest($result);
    }

    /**
     * @param array $response
     * @return array|null
     */
    protected function parseKeywordsFromRequest($response)
    {
        if (!isset($response['guides'])) {
            return null;
        }

        $keywords = $response['guides'];

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