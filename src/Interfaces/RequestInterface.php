<?php

namespace seregazhuk\PinterestBot\Interfaces;

interface RequestInterface
{
    /**
     * Checks if current api user is logged in
     *
     * @return bool
     */
    public function checkLoggedIn();

    /**
     * Executes api call for follow or unfollow pinner, interest or board
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     * @return bool
     */
    public function followMethodCall($entityId, $entityName, $url);


    /**
     * Mark API as logged in.
     */
    public function setLoggedIn();

    /**
     * Executes request to Pinterest API
     *
     * @param string $resourceUrl
     * @param string $postString
     * @return array
     */
    public function exec($resourceUrl, $postString = "");


    /**
     * Executes search to API. Query - search string.
     *
     * @param       $query
     * @param       $scope
     * @param array $bookmarks
     * @return array
     */
    public function searchCall($query, $scope, $bookmarks = []);

    /**
     * Clear token information
     *
     * @return mixed
     */
    public function clearToken();

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
     */
    public function checkErrorInResponse($response);

    /**
     * Get log status
     *
     * @return bool
     */
    public function isLoggedIn();

    /**
     * Executes search to API with pagination.
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Iterator
     */
    public function searchWithPagination($query, $scope, $batchesLimit);
}
