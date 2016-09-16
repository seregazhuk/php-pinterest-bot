<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use Iterator;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Exceptions\WrongFollowingType;

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
     * Get following info for pinner.
     *
     * @param string $username
     * @param string $type
     * @param int $limit
     * @return Iterator
     * @throws WrongFollowingType
     */
    public function following($username, $type = UrlBuilder::FOLLOWING_PEOPLE, $limit = 0)
    {
        $followingUrl = UrlBuilder::getFollowingUrlByType($type);

        if(empty($followingUrl)) {
            throw new WrongFollowingType("No following results for $type");
        }

        return $this->paginate($username, $followingUrl, $limit);
    }

    /**
     * Get following people for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Iterator
     */
    public function followingPeople($username, $limit = 0)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_PEOPLE, $limit);
    }

    /**
     * Get following boards for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Iterator
     */
    public function followingBoards($username, $limit = 0)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_BOARDS, $limit);
    }

    /**
     * Get following interests for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Iterator
     * @throws WrongFollowingType
     */
    public function followingInterests($username, $limit = 0)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_INTERESTS, $limit);
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
     * Get pins that user likes.
     *
     * @param string $username
     * @param int $limit
     * @return Iterator
     */
    public function likes($username, $limit = 0)
    {
        return $this->paginate(
            $username, UrlBuilder::RESOURCE_USER_LIKES, $limit
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
