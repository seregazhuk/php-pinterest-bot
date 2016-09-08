<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\Searchable;

class Pinners extends Provider
{
    use Searchable, Followable;

    protected $loginRequiredFor = ['follow', 'unFollow'];

    protected $searchScope  = 'people';
    protected $entityIdName = 'user_id';
    protected $followersFor = 'username';

    protected $followUrl    = UrlBuilder::RESOURCE_FOLLOW_USER;
    protected $unFollowUrl  = UrlBuilder::RESOURCE_UNFOLLOW_USER;
    protected $followersUrl = UrlBuilder::RESOURCE_USER_FOLLOWERS;
    
    /**
     * Get user info.
     * If username param is not specified, will
     * return info for logged user.
     *
     * @param string $username
     *
     * @return array
     */
    public function info($username)
    {
        return $this->execGetRequest(['username' => $username], UrlBuilder::RESOURCE_USER_INFO);
    }

    /**
     * Get pinner following other pinners.
     *
     * @param string $username
     * @param int $limit
     *
     * @return Iterator
     */
    public function following($username, $limit = 0)
    {
        return $this->paginate(
            $username, UrlBuilder::RESOURCE_USER_FOLLOWING, $limit
        );
    }

    /**
     * Get pinner pins.
     *
     * @param string $username
     * @param int $limit
     *
     * @return Iterator
     */
    public function pins($username, $limit = 0)
    {
        return $this->paginate(
            $username, UrlBuilder::RESOURCE_USER_PINS, $limit
        );
    }

    /**
     * @param string $username
     * @param string $url
     * @param int $limit
     *
     * @return Iterator
     */
    protected function paginate($username, $url, $limit)
    {
        $params = [
            'data' => ['username' => $username],
            'url'  => $url,
        ];

        return $this->getPaginatedResponse($params, $limit);
    }
}
