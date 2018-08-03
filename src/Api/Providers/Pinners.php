<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Api\Traits\Searchable;
use seregazhuk\PinterestBot\Exceptions\WrongFollowingType;
use seregazhuk\PinterestBot\Api\Traits\ResolvesCurrentUser;
use seregazhuk\PinterestBot\Api\Providers\Core\FollowableProvider;

class Pinners extends FollowableProvider
{
    use Searchable, ResolvesCurrentUser;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'block',
        'search',
        'blockById',
        'followers',
        'following',
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
        return $this->get(UrlBuilder::RESOURCE_USER_INFO, ['username' => $username]);
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

        if ($followingUrl === null) {
            throw new WrongFollowingType("No following results for $type");
        }

        return $this->paginateByUsername($username, $followingUrl, $limit);
    }

    /**
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
     * @return bool
     */
    public function block($username)
    {
        // Retrieve profile data to get user id
        $profile = $this->info($username);

        if (empty($profile)) {
            return false;
        }

        return $this->blockById($profile['id']);
    }

    /**
     * @param int $userId
     * @return bool
     */
    public function blockById($userId)
    {
        return $this->post(
            UrlBuilder::RESOURCE_BLOCK_USER, ['blocked_user_id' => $userId]
        );
    }

    /**
     * @param string $username
     * @param int $limit
     * @return Pagination
     */
    public function tried($username, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(UrlBuilder::RESOURCE_USER_TRIED, ['username' => $username], $limit);
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
        return $this->paginate($url, ['username' => $username], $limit);
    }

    /**
     * @param mixed $entityId
     * @return int|null
     */
    protected function resolveEntityId($entityId)
    {
        // If user's id was passed we simply return it.
        if (is_numeric($entityId)) {
            return $entityId;
        }

        // Then we try to get user's info by username
        $userInfo = $this->info($entityId);

        // On success return users'id from his profile.
        return $userInfo['id'] ?? null;
    }

    /**
     * Returns current user's followers when used without arguments.
     * @param string $username
     * @param int $limit
     * @return array|Pagination
     */
    public function followers($username = '', $limit = Pagination::DEFAULT_LIMIT)
    {
        $username = empty($username) ?
            $this->resolveCurrentUsername() :
            $username;

        if (!$username) {
            return new Pagination();
        }

        return parent::followers($username, $limit);
    }

    /**
     * @param string $username
     * @return bool|null
     */
    public function isFollowedByMe($username)
    {
        return $this->info($username)['explicitly_followed_by_me'] ?? false;
    }
}
