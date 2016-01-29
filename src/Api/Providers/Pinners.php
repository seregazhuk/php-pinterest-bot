<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use LogicException;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\FollowersTrait;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Requests\PinnerHelper;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\SearchTrait;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\FollowTrait;

class Pinners extends Provider
{
    use SearchTrait, FollowTrait, FollowersTrait;

    /**
     * Get different user data, for example, followers, following, pins.
     * Collects data while paginating with bookmarks through pinterest results.
     * Return array. Key data - for results and key bookmarks - for pagination.
     *
     * @param string $username
     * @param string $url
     * @param string $sourceUrl
     * @param array $bookmarks
     * @return array
     */
    public function getUserData($username, $url, $sourceUrl, $bookmarks = [])
    {
        $data = ['options' => ['username' => $username]];
        $get = Request::createRequestData($data, $sourceUrl, $bookmarks);
        $getString = UrlHelper::buildRequestString($get);
        $response = $this->request->exec($url.'?'.$getString);

        return $this->response->getPaginationData($response);
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
        $res = $this->getUserData($username, UrlHelper::RESOURCE_USER_INFO, "/$username/");

        return isset($res['data']) ? $res['data'] : null;
    }

    /**
     * Get pinner followers
     *
     * @param string $username
     * @param int $batchesLimit
     * @return Iterator
     */
    public function followers($username, $batchesLimit = 0)
    {
        return $this->getPaginatedData(
            $username, UrlHelper::RESOURCE_USER_FOLLOWERS, "/$username/followers/", $batchesLimit
        );
    }

    /**
     * Get pinner following other pinners
     *
     * @param string $username
     * @param int $batchesLimit
     * @return Iterator
     */
    public function following($username, $batchesLimit = 0)
    {
        return $this->getPaginatedData(
            $username, UrlHelper::RESOURCE_USER_FOLLOWING, "/$username/following/", $batchesLimit
        );
    }

    /**
     * Get pinner pins
     *
     * @param string $username
     * @param int $batchesLimit
     * @return Iterator
     */
    public function pins($username, $batchesLimit = 0)
    {
        return $this->getPaginatedData(
            $username, UrlHelper::RESOURCE_USER_PINS, "/$username/$username/", $batchesLimit
        );
    }

    /**
     * Login as pinner
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password)
    {
        if ($this->request->isLoggedIn()) {
            return true;
        }

        $this->_checkCredentials($username, $password);

        $post = PinnerHelper::createLoginRequest($username, $password);
        $postString = UrlHelper::buildRequestString($post);
        $this->request->clearToken();
        $result = $this->response->checkErrorInResponse($this->request->exec(UrlHelper::RESOURCE_LOGIN, $postString));
        if ($result) {
            $this->request->setLoggedIn();
        }

        return $result;
    }

    /**
     * Validates password and login
     * @param string $username
     * @param string $password
     */
    protected function _checkCredentials($username, $password)
    {
        if ( ! $username || ! $password) {
            throw new LogicException('You must set username and password to login.');
        }
    }

    /**
     * Search scope
     *
     * @return string
     */
    protected function getScope()
    {
        return 'people';
    }

    protected function getEntityIdName()
    {
        return Request::PINNER_ENTITY_ID;
    }

    /**
     * Follow resource
     *
     * @return string
     */
    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_USER;
    }

    /**
     * UnFollow resource
     * @return string
     */
    protected function getUnfFollowUrl()
    {
        return UrlHelper::RESOURCE_UNFOLLOW_USER;
    }

    /**
     * @param string $username
     * @param string $url
     * @param string $sourceUrl
     * @param integer $batchesLimit
     * @return Iterator
     */
    protected function getPaginatedData($username, $url, $sourceUrl, $batchesLimit)
    {
        $data = [
            ['username' => $username],
            $url,
            $sourceUrl
        ];

        return Pagination::getPaginatedData([$this, 'getData'], $data, $batchesLimit);
    }
}
