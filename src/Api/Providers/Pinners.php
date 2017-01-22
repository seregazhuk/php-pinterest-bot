<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Traits\Followable;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Exceptions\WrongFollowingType;

class Pinners extends EntityProvider
{
    use Searchable, Followable;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'follow',
        'block',
        'unFollow',
        'blockById',
    ];

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
     * @return Pagination
     * @throws WrongFollowingType
     */
    public function following($username, $type = UrlBuilder::FOLLOWING_PEOPLE, $limit = Pagination::DEFAULT_LIMIT)
    {
        $followingUrl = UrlBuilder::getFollowingUrlByType($type);

        if(empty($followingUrl)) {
            throw new WrongFollowingType("No following results for $type");
        }

        return $this->paginateByUsername($username, $followingUrl, $limit);
    }

    /**
     * @codeCoverageIgnore
     * Get following people for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Pagination
     */
    public function followingPeople($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_PEOPLE, $limit);
    }

    /**
     * @codeCoverageIgnore
     * Get following boards for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Pagination
     */
    public function followingBoards($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_BOARDS, $limit);
    }

    /**
     * @codeCoverageIgnore
     * Get following interests for pinner.
     *
     * @param string $username
     * @param int $limit
     * @return Pagination
     * @throws WrongFollowingType
     */
    public function followingInterests($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->following($username, UrlBuilder::FOLLOWING_INTERESTS, $limit);
    }

    /**
     * Get pinner pins.
     *
     * @param string $username
     * @param int $limit
     *
     * @return Pagination
     */
    public function pins($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginateByUsername(
            $username, UrlBuilder::RESOURCE_USER_PINS, $limit
        );
    }

    /**
     * Get pins that user likes.
     *
     * @param string $username
     * @param int $limit
     * @return Pagination
     */
    public function likes($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginateByUsername(
            $username, UrlBuilder::RESOURCE_USER_LIKES, $limit
        );
    }

    /**
     * @param string $username
     * @return bool|Response
     */
    public function block($username)
    {
        // Retrieve profile data to get user id
        $profile = $this->info($username);

        if(empty($profile)) return false;

        return $this->blockById($profile['id']);
    }

    /**
     * @param int $userId
     * @return bool|Response
     */
    public function blockById($userId)
    {
        $data = ['blocked_user_id' => $userId];

        return $this->execPostRequest($data, UrlBuilder::RESOURCE_BLOCK_USER);
    }

    /**
     * @param string $username
     * @param string $url
     * @param int $limit
     *
     * @return Pagination
     */
    protected function paginateByUsername($username, $url, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(['username' => $username], $url, $limit);
    }
}
