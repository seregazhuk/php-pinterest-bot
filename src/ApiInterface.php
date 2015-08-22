<?php

namespace seregazhuk\PinterestBot;


interface ApiInterface
{
    /**
     * Executes api call to pinterest
     *
     * @param            $url
     * @param string     $postString
     * @return array
     */
    public function exec($url, $postString = "");

    /**
     * Checks if current api user is logged in
     *
     * @return bool
     */
    public function isLoggedIn();

    /**
     * @param string $csrfToken Pinterest security token. Mark api as logged
     */
    public function setLoggedIn($csrfToken);

    /**
     * Get requests cookieJar
     *
     * @return mixed
     */
    public function getCookieJar();


    /**
     * Executes api call for follow or unfollow pinner or board
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     * @return bool
     */
    public function followMethodCall($entityId, $entityName, $url);


    /**
     * Clear token information
     *
     * @return mixed
     */
    public function clearToken();

}