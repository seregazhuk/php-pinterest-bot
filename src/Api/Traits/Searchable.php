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
    public function execSearchRequest($query, $scope, $bookmarks = [])
    {
        $url = UrlBuilder::getSearchUrl($bookmarks);
        $get = $this->createSearchQuery($query, $scope, $bookmarks);
        $result = $this->request->exec($url . '?' . $get);

        $this->processResult($result);

        return (new SearchResponse())->fillFromJson($result);
    }

    /**
     * Creates Pinterest API search request.
     *
     * @param string $query
     * @param string $scope
     * @param array $bookmarks
     *
     * @return string
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
     * @return Pagination
     */
    public function search($query, $limit = Pagination::DEFAULT_LIMIT)
    {
        return (new Pagination($limit))
            ->paginateOver(function($bookmarks = []) use ($query) {
                return $this->execSearchRequest($query, $this->getSearchScope(), $bookmarks);
            });
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
     * @param string $res
     * @return Response
     */
    abstract protected function processResult($res);
}
