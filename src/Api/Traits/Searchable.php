<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Trait Searchable
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $searchScope
 */
trait Searchable
{
    use HandlesRequest;
    
    private $moduleSearchPage = 'SearchPage';

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
     * @return array
     */
    public function searchCall($query, $scope, $bookmarks = [])
    {
        $url = UrlHelper::getSearchUrl($bookmarks);
        $get = $this->createSearchQuery($query, $scope, $bookmarks);
        $response = $this->getRequest()->exec($url . '?' . $get);

        /*
         * It was a first time search, we grab data and bookmarks for pagination.
         */
        if (empty($bookmarks)) {
            return $this->parseResponseSearchResult($response);
        }

        /*
         * Process a response with bookmarks
         */

        return $response->getPaginationData();
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
            $bookmarks, ['scope' => $scope, 'query' => $query]
        );

        $request = Request::createRequestData($dataJson, $bookmarks);

        return UrlHelper::buildRequestString($request);
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
        return (new Pagination($this))->paginateOver(
            'searchCall', [
            'query' => $query,
            'scope' => $this->getSearchScope(),
        ], $limit
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
        } else {
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
    }

    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks.
     *
     * @param Response $response
     *
     * @return array
     */
    protected function parseResponseSearchResult(Response $response)
    {
        $bookmarks = $response->getData('module.tree.resource.options.bookmarks', []);
        $results = $response->getData('module.tree.data.results');

        if (!empty($results)) {
            return ['data' => $results, 'bookmarks' => [$bookmarks]];
        }

        return [];
    }
}
