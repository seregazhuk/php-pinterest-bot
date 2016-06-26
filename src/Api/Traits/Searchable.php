<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class Searchable
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $searchScope
 */
trait Searchable
{
    use HandlesRequestAndResponse;
    
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
        $result = $this->getRequest()->exec($url . '?' . $get);

        /*
         * It was a first time search, we grab data and bookmarks for pagination.
         */
        if (empty($bookmarks)) {
            return $this->parseSearchResult($result);
        }

        /*
         * Process a response with bookmarks
         */

        return $this->getResponse()->getPaginationData($result);
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
        $options = ['scope' => $scope, 'query' => $query];
        $dataJson = $this->appendBookMarks($bookmarks, $options);

        print_r($dataJson);
        die();
        return Request::createQuery($dataJson);
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
        if (!empty($bookmarks)) {
            $options['bookmarks'] = $bookmarks;

            return $options;
        }

        $dataJson = array_merge(
            $options, [
                'module' => [
                    'name'    => $this->moduleSearchPage,
                    'options' => $options,
                ],
            ]
        );

        return $dataJson;
    }

    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks.
     *
     * @param array $response
     *
     * @return array
     */
    protected function parseSearchResult($response)
    {
        $bookmarks = [];

        if (isset($response['module']['tree']['resource']['options']['bookmarks'][0])) {
            $bookmarks = $response['module']['tree']['resource']['options']['bookmarks'][0];
        }

        if (!empty($response['module']['tree']['data']['results'])) {
            return ['data' => $response['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
        }

        return [];
    }
}
