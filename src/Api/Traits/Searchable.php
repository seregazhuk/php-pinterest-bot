<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\SearchResponse;

/**
 * Trait Searchable
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $searchScope
 */
trait Searchable
{
    use HandlesRequest;
    
    protected $moduleSearchPage = 'SearchPage';

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
     * @param array  $bookmarks
     *
     * @return SearchResponse
     */
    public function searchCall($query, $scope, $bookmarks = [])
    {
        $url = UrlBuilder::getSearchUrl($bookmarks);
        $get = $this->createSearchQuery($query, $scope, $bookmarks);
        $result = $this->request->exec($url . '?' . $get);

        $this->processResult($result);

        return new SearchResponse($this->response);
    }

    /**
     * Creates Pinterest API search request.
     *
     * @param string $query
     * @param string $scope
     * @param array $bookmarks
     *
     * @return array
     */
    protected function createSearchQuery($query, $scope, $bookmarks = [])
    {
        $dataJson = $this->appendBookMarks(
            $bookmarks,
            [
                'scope' => $scope,
                'query' => $query
            ]
        );

        $request = Request::createRequestData($dataJson, $bookmarks);

        return UrlBuilder::buildRequestString($request);
    }

    /**
     * Search entities by search query.
     *
     * @param string $query
     * @param int $limit
     *
     * @return \Iterator
     */
    public function search($query, $limit = 0)
    {
        return $this->getPaginatedResponse(
            [
                'query' => $query,
                'scope' => $this->getSearchScope(),
            ],
            $limit,
            'searchCall'
        );
    }

    /**
     * @param array $bookmarks
     * @param array $options
     *
     * @return array
     */
    protected function appendBookMarks($bookmarks, $options)
    {
        $dataJson = ['options' => $options];
        if (!empty($bookmarks)) {
            $dataJson['options']['bookmarks'] = $bookmarks;

            return $dataJson;
        }

        $dataJson = array_merge(
            $dataJson, [
                'module' => [
                    "name"    => $this->moduleSearchPage,
                    "options" => $options,
                ],
            ]
        );

        return $dataJson;
    }

    /**
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return mixed
     */
    abstract protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData');
}
