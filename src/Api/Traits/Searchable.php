<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\SearchResponse;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait Searchable
 *
 * @property string $searchScope
 */
trait Searchable
{
    use HasPagination, HandlesRequest;

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
     * @param array $bookmarks
     * @return SearchResponse
     */
    protected function execSearchRequest($query, $scope, $bookmarks = [])
    {
        $url = empty($bookmarks) ?
            UrlBuilder::RESOURCE_SEARCH :
            UrlBuilder::RESOURCE_SEARCH_WITH_PAGINATION;


        $requestOptions = [
            'scope' => $scope,
            'query' => $query,
        ];

        $response = $this->get($url, $requestOptions, $bookmarks);
        return new SearchResponse($response->getRawData());
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
        $bookmarks = [];
        return $this->paginateCustom(function () use ($query, &$bookmarks) {
            $response = $this->execSearchRequest($query, $this->getSearchScope(), $bookmarks);
            $bookmarks = $response->getBookmarks();
            return $response;
        })->take($limit);
    }
}
