<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\SearchResponse;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait Searchable
 *
 * @property string $searchScope
 * @property Request request
 * @property Response $response
 */
trait Searchable
{
    /**
     * @return string
     */
    protected function getSearchScope()
    {
        return property_exists($this, 'searchScope') ? $this->searchScope : '';
    }

    /**
     * Executes search to API. Query - search string.
     *
     * @param string $query
     * @param string $scope
     * @return SearchResponse
     */
    protected function execSearchRequest($query, $scope)
    {
        $url = $this->response->hasBookmarks() ?
            UrlBuilder::RESOURCE_SEARCH_WITH_PAGINATION :
            UrlBuilder::RESOURCE_SEARCH;

        $requestOptions = [
            'scope' => $scope,
            'query' => $query,
        ];

        $this->get($requestOptions, $url);

        return new SearchResponse(
            $this->response->getRawData()
        );
    }

    /**
     * Search entities by search query.
     *
     * @param string $query
     * @param int $limit
     *
     * @return Pagination
     */
    public function search($query, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginateCustom(function() use ($query) {
                return $this->execSearchRequest($query, $this->getSearchScope());
            })->take($limit);
    }
}
