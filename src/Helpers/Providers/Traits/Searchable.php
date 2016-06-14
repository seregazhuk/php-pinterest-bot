<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;

trait Searchable
{
    private $moduleSearchPage = 'SearchPage';

    /**
     * @return string
     */
    abstract protected function getScope();

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
     * @param       $query
     * @param       $scope
     * @param array $bookmarks
     *
     * @return array
     */
    protected function createSearchQuery($query, $scope, $bookmarks = [])
    {
        $options = ['scope' => $scope, 'query' => $query];
        $dataJson = $this->appendBookMarks($bookmarks, $options);

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
            'scope' => $this->getScope(),
        ], $limit
        );
    }

    /**
     * @param $bookmarks
     * @param $options
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
