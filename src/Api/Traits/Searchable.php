<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\SearchResponse;

/**
 * Trait Searchable
 *
 * @property string $searchScope
 * @property Request request
 * @property Response $response
 */
trait Searchable
{
    use HandlesRequest;
    
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
    public function execSearchRequest($query, $scope)
    {
        $url = UrlBuilder::getSearchUrl(
            $this->response->getBookmarks()
        );

        $get = $this->createSearchQuery($query, $scope);

        $this->execute($url . '?', $get);

        return new SearchResponse(
            $this->response->getRawData()
        );
    }

    /**
     * Creates Pinterest API search request.
     *
     * @param string $query
     * @param string $scope
     * @return string
     */
    protected function createSearchQuery($query, $scope)
    {
        $dataJson = $this->appendBookmarks(
            [
                'scope' => $scope,
                'query' => $query
            ]
        );

        $request = Request::createRequestData(
            $dataJson, $this->response->getBookmarks()
        );

        return UrlBuilder::buildRequestString($request);
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
        return (new Pagination($limit))
            ->paginateOver(function() use ($query) {
                return $this->execSearchRequest($query, $this->getSearchScope());
            });
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function appendBookmarks($options)
    {
        $dataJson = ['options' => $options];
        if ($this->response->hasBookmarks()) {
            $dataJson['options']['bookmarks'] = $this->response->getBookmarks();

            return $dataJson;
        }

        $dataJson = array_merge(
            $dataJson, [
                'module' => [
                    "name"    => 'SearchPage',
                    "options" => $options,
                ],
            ]
        );

        return $dataJson;
    }

    /**
     * @param string $url
     * @param string $postString
     * @return $this
     */
    abstract protected function execute($url, $postString = "");
}
