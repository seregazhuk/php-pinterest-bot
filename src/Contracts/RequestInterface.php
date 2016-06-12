<?php

namespace seregazhuk\PinterestBot\Contracts;

interface RequestInterface
{
    /**
     * Executes api call for follow or unfollow pinner, interest or board.
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     *
     * @return array
     */
    public function followMethodCall($entityId, $entityName, $url);

    /**
     * Set status to logged in.
     *
     * @return $this
     */
    public function login();

    /**
     * Set status to logged out.
     *
     * @return $this
     */
    public function logout();


    /**
     * Executes request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param string $postString
     *
     * @return array
     */
    public function exec($resourceUrl, $postString = '');

    /**
     * Clear token information.
     */
    public function clearToken();

    /**
     * Get logged in status.
     *
     * @return bool
     */
    public function isLoggedIn();
}
