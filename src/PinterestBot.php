<?php

namespace szhuk\PinterestAPI;

use szhuk\PinterestAPI\helpers\BoardHelper;
use szhuk\PinterestAPI\helpers\PaginationHelper;
use szhuk\PinterestAPI\helpers\PinHelper;
use szhuk\PinterestAPI\helpers\PinnerHelper;
use szhuk\PinterestAPI\helpers\SearchHelper;
use szhuk\PinterestAPI\helpers\UrlHelper;
use szhuk\PinterestAPI\helpers\CsrfHelper;

/**
 * Class PinterestBot
 *
 * @package Pinterest
 * @property string       $username
 * @property string       $password
 * @property ApiInterface $api
 * @property int          $lastApiErrorCode
 * @property string       $lastApiErrorMsg
 */
class PinterestBot
{
    public $username;
    public $password;

    /**
     * @var ApiInterface
     */
    protected $api;

    public $lastApiErrorCode;
    public $lastApiErrorMsg;

    const SEARCH_PINS_SCOPES   = 'pins';
    const SEARCH_PEOPLE_SCOPES = 'people';
    const SEARCH_BOARDS_SCOPES = 'boards';

    const MAX_PAGINATED_ITEMS = 100;

    public function __construct($username, $password, ApiInterface $api)
    {
        $this->username = $username;
        $this->password = $password;
        $this->api      = $api;
    }

    /**
     * Login and parsing csrfToken from cookies if success
     */
    public function login()
    {
        if ( ! $this->username || ! $this->password) {
            throw new \LogicException('You must set username and password to login.');
        }

        $post = PinnerHelper::createLoginRequest($this->username, $this->password);
        $postString = UrlHelper::buildRequestString($post);
        $res  = $this->api->exec(UrlHelper::RESOURCE_LOGIN,
            $postString,
            "https://www.pinterest.com/login/",
            [
                'X-CSRFToken: 1234',
                'Cookie: csrftoken=1234;',
            ],
            false,
            false);

        return PinnerHelper::parseLoginResponse($res, $this->api);
    }

    /**
     * Get all logged-in user boards
     *
     * @return array|null
     */
    public function getBoards()
    {
        $this->checkLoggedIn();
        $get = BoardHelper::createBoardsInfoRequest();
        $getString = UrlHelper::buildRequestString($get);
        $res = $this->api->exec(UrlHelper::RESOURCE_GET_BOARDS . "?{$getString}", "");

        return BoardHelper::parseBoardsInfoResponse($res);
    }

    /**
     * Get the logged-in account username
     *
     * @return array|null
     */
    public function getAccountName()
    {
        $this->checkLoggedIn();
        $res = $this->api->exec(UrlHelper::RESOURCE_GET_ACCOUNT_NAME);

        return PinnerHelper::parseAccountNameResponse($res);
    }

    /**
     * Follow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function followUser($userId)
    {
        $this->checkLoggedIn();
        return $this->api->followMethodCall($userId, "user_id", UrlHelper::RESOURCE_FOLLOW_USER);
    }

    /**
     * Unfollow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function unFollowUser($userId)
    {
        $this->checkLoggedIn();
        return $this->api->followMethodCall($userId, "user_id", UrlHelper::RESOURCE_UNFOLLOW_USER);
    }

    /**
     * Likes pin with current ID
     *
     * @param integer $pinId
     * @return bool
     */
    public function likePin($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_LIKE_PIN);
    }


    /**
     * Removes your like from pin with current ID
     *
     * @param integer $pinId
     * @return bool
     */
    public function unLikePin($pinId)
    {
        return $this->likePinMethodCall($pinId, UrlHelper::RESOURCE_UNLIKE_PIN);
    }


    /**
     * Calls pinterest API to like or unlike Pin by ID
     *
     * @param $pinId
     * @param $url
     * @return bool
     */
    protected function likePinMethodCall($pinId, $url)
    {
        $this->checkLoggedIn();
        $post = PinHelper::createLikeRequest($pinId);
        $postString = URlHelper::buildRequestString($post);
        $res  = $this->api->exec($url, $postString, "pin/$pinId/");

        return PinHelper::checkMethodCallResult($res);
    }

    /**
     * Writes comment for pin with current id
     *
     * @param integer $pinId
     * @param string  $text Comment
     * @return bool
     */
    public function commentPin($pinId, $text)
    {
        $this->checkLoggedIn();
        $post = PinHelper::createCommentRequest($pinId, $text);
        $postString = UrlHelper::buildRequestString($post);
        $res  = $this->api->exec(UrlHelper::RESOURCE_COMMENT_PIN, $postString,
            UrlHelper::URL_BASE . "pin/$pinId/");

        return PinHelper::checkMethodCallResult($res);
    }

    /**
     * Checks if bot is logged in
     *
     * @throws \LogicException if is not logged in
     */
    public function checkLoggedIn()
    {
        if ( ! $this->api->isLoggedIn()) {
            throw new \LogicException("You must log in before.");
        }
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
        $this->checkLoggedIn();

        $get = PinnerHelper::createUserDataRequest($username, $sourceUrl, $bookmarks);
        $getString = UrlHelper::buildRequestString($get);
        $res = $this->api->exec($url . '?' . $getString, "", $username);
        $this->checkErrorInResponse($res);

        return PinnerHelper::checkUserDataResponse($res);
    }

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
     */
    public function checkErrorInResponse($response)
    {
        $this->lastApiErrorCode = null;
        $this->lastApiErrorMsg  = null;

        if (isset($response['api_error_code'])) {
            $this->lastApiErrorCode = $response['api_error_code'];
            $this->lastApiErrorMsg  = $response['message'];
        }
    }

    /**
     * Get user info
     * If username param is not specified, will
     * return info for logged user
     *
     * @param string $username
     * @return null|array
     */
    public function getUserInfo($username)
    {
        $res = $this->getUserData($username,
            UrlHelper::RESOURCE_USER_INFO,
            "/$username/"
        );

        return isset($res['data']) ? $res['data'] : null;
    }

    /**
     * Create pin. Returns created pin ID
     *
     * @param string $imageUrl
     * @param string $imagePreview
     * @param int    $boardId
     * @param string $description
     * @return bool|int
     */
    public function pin($imageUrl, $boardId, $description = "", $imagePreview = "")
    {
        $this->checkLoggedIn();
        $post = PinHelper::createPinCreationRequest($imageUrl, $boardId, $description, $imagePreview);
        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_CREATE_PIN, $postString);

        $this->checkErrorInResponse($res);

        return PinHelper::parsePinCreateResponse($res);
    }

    /**
     * Repin
     *
     * @param int    $repinId
     * @param int    $boardId
     * @param string $description
     * @return bool|int
     */
    public function repin($repinId, $boardId, $description = "")
    {
        $this->checkLoggedIn();

        $post = PinHelper::createRepinRequest($repinId, $boardId, $description);
        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_REPIN, $postString);
        $this->checkErrorInResponse($res);

        return PinHelper::parsePinCreateResponse($res);
    }


    /**
     * Delete pin
     *
     * @param int $pinId
     * @return bool
     */
    public function deletePin($pinId)
    {
        $this->checkLoggedIn();

        $post = PinHelper::createDeleteRequest($pinId);
        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_DELETE_PIN, $postString);

        $this->checkErrorInResponse($res);

        return $res ? true : false;
    }


    /**
     * Get pinner followers
     *
     * @param string $username
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function getFollowers($username, $batchesLimit = 0)
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
    public function getFollowing($username, $batchesLimit = 0)
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
    public function getUserPins($username, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'getUserData',
            ['username' => $username, 'url' => UrlHelper::RESOURCE_USER_PINS, 'sourceUrl' => "/$username/pins/"],
            $batchesLimit);
    }


    /**
     * Executes search to API. Query - search string.
     *
     * @param       $query
     * @param       $scope
     * @param array $bookmarks
     * @return array
     */
    public function search($query, $scope, $bookmarks = [])
    {
        $url = UrlHelper::getSearchUrl(! empty($bookmarks));
        $get = SearchHelper::createSearchRequest($query, $scope, $bookmarks);
        $url = $url . '?' . UrlHelper::buildRequestString($get);
        $res = $this->api->exec($url);
        return SearchHelper::parseSearchResponse($res, ! empty($bookmarks));
    }


    /**
     * Search pinners by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function searchPinners($query, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'search',
            ['query' => $query, 'scope' => self::SEARCH_PEOPLE_SCOPES],
            $batchesLimit);
    }

    /**
     * Search pins by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function searchPins($query, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'search',
            ['query' => $query, 'scope' => self::SEARCH_PINS_SCOPES],
            $batchesLimit);
    }

    /**
     * Search boards by search query
     *
     * @param string $query
     * @param int    $batchesLimit
     * @return \Generator
     */
    public function searchBoards($query, $batchesLimit = 0)
    {
        return PaginationHelper::getPaginatedData($this, 'search',
            ['query' => $query, 'scope' => self::SEARCH_BOARDS_SCOPES],
            $batchesLimit);
    }

    /**
     * Get information of pin by PinID
     *
     * @param $pinId
     * @return array|null;
     */
    public function getPinInfo($pinId)
    {
        $get = PinHelper::createInfoRequest($pinId);
        $url = UrlHelper::RESOURCE_PIN_INFO . '?' . UrlHelper::buildRequestString($get);
        $res = $this->api->exec($url);

        return PinHelper::parsePinInfoResponse($res);
    }

    /**
     * Follow board by boardID
     *
     * @param $boardId
     * @return bool
     */
    public function followBoard($boardId)
    {
        $this->checkLoggedIn();

        return $this->api->followMethodCall($boardId, "board_id", UrlHelper::RESOURCE_FOLLOW_BOARD);

    }

    /**
     * Unfollow board by boardID
     *
     * @param $boardId
     * @return bool
     */
    public function unFollowBoard($boardId)
    {
        $this->checkLoggedIn();

        return $this->api->followMethodCall($boardId, "board_id", UrlHelper::RESOURCE_UNFOLLOW_BOARD);

    }
}