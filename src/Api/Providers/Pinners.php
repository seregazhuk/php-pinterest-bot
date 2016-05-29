<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use LogicException;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Helpers\Requests\PinnerHelper;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Followable;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\Searchable;
use seregazhuk\PinterestBot\Helpers\Providers\Traits\HasFollowers;

class Pinners extends Provider
{
    use Searchable, Followable, HasFollowers;

    protected $loginRequired = [
        'follow',
        'unFollow',
    ];

    /**
     * Get user info.
     * If username param is not specified, will
     * return info for logged user.
     *
     * @param string $username
     *
     * @return null|array
     */
    public function info($username)
    {
        $res = $this->paginate($username, UrlHelper::RESOURCE_USER_INFO, "/$username/", 1);
        $res = iterator_to_array($res);

        return !empty($res) ? $res[0] : null;
    }

    /**
     * Get pinner followers.
     *
     * @param string $username
     * @param int    $batchesLimit
     *
     * @return Iterator
     */
    public function followers($username, $batchesLimit = 0)
    {
        return $this->paginate(
            $username, UrlHelper::RESOURCE_USER_FOLLOWERS, "/$username/followers/", $batchesLimit
        );
    }

    /**
     * Get pinner following other pinners.
     *
     * @param string $username
     * @param int    $batchesLimit
     *
     * @return Iterator
     */
    public function following($username, $batchesLimit = 0)
    {
        return $this->paginate(
            $username, UrlHelper::RESOURCE_USER_FOLLOWING, "/$username/following/", $batchesLimit
        );
    }

    /**
     * Get pinner pins.
     *
     * @param string $username
     * @param int    $batchesLimit
     *
     * @return Iterator
     */
    public function pins($username, $batchesLimit = 0)
    {
        return $this->paginate(
            $username, UrlHelper::RESOURCE_USER_PINS, "/$username/$username/", $batchesLimit
        );
    }

    /**
     * Login as pinner.
     *
     * @param string $username
     * @param string $password
     *
     * @throws AuthException
     *
     * @return bool
     */
    public function login($username, $password)
    {
        if ($this->request->isLoggedIn()) {
            return true;
        }

        $this->checkCredentials($username, $password);

        $postString = PinnerHelper::createLoginQuery($username, $password);
        $this->request->clearToken();

        $response = $this->request->exec(UrlHelper::RESOURCE_LOGIN, $postString);
        $result = $this->response->checkErrorInResponse($response);
        if (!$result) {
            throw new AuthException($this->response->getLastError()['message']);
        }
        $this->request->setLoggedIn();

        return $result;
    }

    /**
     * Validates password and login.
     *
     * @param string $username
     * @param string $password
     */
    protected function checkCredentials($username, $password)
    {
        if (!$username || !$password) {
            throw new LogicException('You must set username and password to login.');
        }
    }

    /**
     * Search scope.
     *
     * @return string
     */
    protected function getScope()
    {
        return 'people';
    }

    protected function getEntityIdName()
    {
        return 'user_id';
    }

    /**
     * Follow resource.
     *
     * @return string
     */
    protected function getFollowUrl()
    {
        return UrlHelper::RESOURCE_FOLLOW_USER;
    }

    /**
     * UnFollow resource.
     *
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
     * @param int    $batchesLimit
     *
     * @return Iterator
     */
    public function paginate($username, $url, $sourceUrl, $batchesLimit)
    {
        $params = [
            'data'      => ['username' => $username],
            'url'       => $url,
            'sourceUrl' => $sourceUrl,
        ];

        return (new Pagination($this))->run('getPaginatedData', $params, $batchesLimit);
    }
}
