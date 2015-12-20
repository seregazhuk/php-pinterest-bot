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
     * @return array
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
     * Clear token information
     *
     * @return mixed
     */
    public function clearToken();

    /**
     * Get log status
     *
     * @return bool
     */
    public function isLoggedIn();

}
