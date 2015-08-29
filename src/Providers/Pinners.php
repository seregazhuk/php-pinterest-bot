<?php

namespace seregazhuk\PinterestBot\Providers;

use seregazhuk\PinterestBot\Request;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\PaginationHelper;
use seregazhuk\PinterestBot\Helpers\PinnerHelper;

class Pinners extends Provider
{
    /**
     * Follow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function follow($userId)
    {
        $this->request->checkLoggedIn();

        return $this->request->followMethodCall($userId, Request::PINNER_ENTITY_ID, UrlHelper::RESOURCE_FOLLOW_USER);
    }

    /**
     * Unfollow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function unFollow($userId)
    {
        $this->request->checkLoggedIn();

        return $this->request->followMethodCall($userId, Request::PINNER_ENTITY_ID, UrlHelper::RESOURCE_UNFOLLOW_USER);
    }

    /**
     * Get different user data, for example, followers, following, pins.
     * Collects data while paginating with bookmarks through pinterest results.
     * Return array. Key data - for results and key bookmarks - for pagination.
     *
     * @param string $username
     * @param string $url
     * @param string $sourceUrl
     * @param array  $bookmarks
     * @return array
     */
    public function getUserData($username, $url, $sourceUrl, $bookmarks = [])
    {
        $get       = PinnerHelper::createUserDataRequest($username, $sourceUrl, $bookmarks);
        $getString = UrlHelper::buildRequestString($get);
        $res       = $this->request->exec($url . '?' . $getString, $username);
        $this->request->checkErrorInResponse($res);

        return PinnerHelper::checkUserDataResponse($res);
    }

    /**
     * Get the logged-in account username
     *
     * @return array|null
     */
    public function myAccountName()
    {
        $this->request->checkLoggedIn();
        $res = $this->request->exec(UrlHelper::RESOURCE_GET_ACCOUNT_NAME);

        return PinnerHelper::parseAccountNameResponse($res);
    }

    /**
     * Get user info
     * If username param is not specified, will
     * return info for logged user
     *
     * @param string $username
     * @return null|array
     */
    public function info($username)
    {
        $res = $this->getUserData($username,
            UrlHelper::RESOURCE_USER_INFO,
            "/$username/"
        );

        return isset($res['data']) ? $res['data'] : null;
    }

    /**
     * Get pinner followers
     *
     * @param string $username
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function followers($username, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'getUserData',
            [
                'username'  => $username,
                'url'       => UrlHelper::RESOURCE_USER_FOLLOWERS,
                'sourceUrl' => "/$username/followers/",
            ],
            $batchesLimit);
    }

    /**
     * Get pinner following other pinners
     *
     * @param string $username
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function following($username, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'getUserData',
            [
                'username'  => $username,
                'url'       => UrlHelper::RESOURCE_USER_FOLLOWING,
                'sourceUrl' => "/$username/following/",
            ],
            $batchesLimit);
    }

    /**
     * Get pinner pins
     *
     * @param string $username
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function pins($username, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'getUserData',
            ['username' => $username, 'url' => UrlHelper::RESOURCE_USER_PINS, 'sourceUrl' => "/$username/pins/"],
            $batchesLimit);
    }


    /**
     * Search pinners by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function search($query, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this->request, '_search',
            ['query' => $query, 'scope' => Request::SEARCH_PEOPLE_SCOPE],
            $batchesLimit);
    }

    /**
     * Login as pinner
     *
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        $post       = PinnerHelper::createLoginRequest($username, $password);
        $postString = UrlHelper::buildRequestString($post);
        $this->request->clearToken();
        $res = PinnerHelper::parseLoginResponse($this->request->exec(UrlHelper::RESOURCE_LOGIN, $postString));
        if ($res) {
            $this->request->setLoggedIn();
        }

        return $res;
    }

}