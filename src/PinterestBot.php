<?php

namespace szhuk\PinterestAPI;

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

        $dataJson = [
            "options" => [
                "username_or_email" => $this->username,
                "password"          => $this->password,
            ],
            "context" => [],
        ];
        $post = [
            "source_url" => "/login/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = UrlHelper::buildRequestString($post);
        $res = $this->api->exec(UrlHelper::RESOURCE_LOGIN,
            $postString,
            "https://www.pinterest.com/login/",
            [
                'X-CSRFToken: 1234',
                'Cookie: csrftoken=1234;',
            ],
            false,
            false);

        if ($res === null) {
            return false;
        } else {
            $this->api->setLoggedIn(CsrfHelper::getCsrfToken($this->api->getCookieJar()));

            return true;
        }
    }

    /**
     * Get all logged-in user boards
     *
     * @return array|null
     */
    public function getBoards()
    {
        $this->checkLoggedIn();

        $dataJson = [
            "options" => [
                "filter"        => "all",
                "field_set_key" => "board_picker",
            ],
            "context" => [],
        ];

        $get = [
            "source_url"  => "/pin/create/bookmarklet/?url=",
            "pinFave"     => "1",
            "description" => "",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $getString = UrlHelper::buildRequestString($get);

        $res = $this->api->exec(UrlHelper::RESOURCE_GET_BOARDS . "?{$getString}", "",
            "https://www.pinterest.com/pin/create/bookmarklet/?url=&pinFave=1&description=");

        if (isset($res['resource_response']['data']['all_boards'])) {
            return $res['resource_response']['data']['all_boards'];
        }

        return null;

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
        if (isset($res['resource_data_cache'][1]['resource']['options']['username'])) {
            return $res['resource_data_cache'][1]['resource']['options']['username'];
        }

        return null;
    }

    /**
     * Follow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function followUser($userId)
    {
        return $this->followMethodCall($userId, "user_id", UrlHelper::RESOURCE_FOLLOW_USER);
    }

    /**
     * Unfollow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function unFollowUser($userId)
    {
        return $this->followMethodCall($userId, "user_id", UrlHelper::RESOURCE_UNFOLLOW_USER);
    }

    /**
     * Executes api call for follow or unfollow user
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     * @return bool
     */
    protected function followMethodCall($entityId, $entityName, $url)
    {
        $this->checkLoggedIn();

        $dataJson = [
            "options" => [
                $entityName => $entityId,
            ],
            "context" => [],
        ];
        $post     = [
            "data" => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = UrlHelper::buildRequestString($post);
        $res      = $this->api->exec($url, $postString);

        if ($res === null) {
            return false;
        }

        return true;
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

        $dataJson = [
            "options" => [
                "pin_id" => $pinId,
            ],
            "context" => [],
        ];
        $post     = [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = URlHelper::buildRequestString($post);
        $res = $this->api->exec($url, $postString, "pin/$pinId/");

        return $this->checkPinMethodCallResult($res);
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

        $dataJson = [
            "options" => [
                "pin_id" => $pinId,
                "text"   => $text,
            ],
            "context" => [],
        ];

        $post = [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = UrlHelper::buildRequestString($post);
        $res  = $this->api->exec(UrlHelper::RESOURCE_COMMENT_PIN, $postString,
            UrlHelper::URL_BASE . "pin/$pinId/");

        return $this->checkPinMethodCallResult($res);
    }

    /**
     * Checks result of PIN-methods
     *
     * @param array $res
     * @return bool
     */
    protected function checkPinMethodCallResult($res)
    {
        if ($res !== null && isset($res['resource_response'])) {
            return true;
        } else {
            return false;
        }
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

        $dataJson = [
            "options" => [
                "username" => $username,
            ],
            "context" => new \stdClass(),
        ];

        if ( ! empty($bookmarks)) {
            $dataJson["options"]["bookmarks"] = $bookmarks;
        }

        $get = [
            "source_url" => $sourceUrl,
            "data"       => json_encode($dataJson, true),
        ];

        $getString = UrlHelper::buildRequestString($get);

        $res = $this->api->exec($url . '?' . $getString, "", $username);

        $this->checkErrorInResponse($res);

        if ($res === null) {
            return [];
        } else {

            if (isset($res['resource']['options']['bookmarks'][0])) {
                $bookmarks = [$res['resource']['options']['bookmarks'][0]];
            } else {
                $bookmarks = null;
            }

            if (isset($res['resource_response']['data'])) {
                $data = $res['resource_response']['data'];

                return ['data' => $data, 'bookmarks' => $bookmarks];
            } else {
                return [];
            }
        }
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

        $dataJson = [
            "options" => [
                "method"      => "scraped",
                "description" => $description,
                "link"        => $imageUrl,
                "image_url"   => $imagePreview,
                "board_id"    => $boardId,
            ],
            "context" => new \stdClass(),
        ];

        // And prepare the post data array
        $post = [
            "source_url" => "/pin/find/?url=" . $imageUrl,
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_CREATE_PIN, $postString);

        $this->checkErrorInResponse($res);

        return $this->parsePinCreateResponse($res);
    }

    /**
     * Repin
     *
     * @param int    $repinId
     * @param int    $boardId
     * @param string $description
     * @return bool|int
     */
    public function repin($repinId, $boardId, $description)
    {
        $this->checkLoggedIn();

        $dataJson = [
            "options" => [
                "board_id"    => $boardId,
                "description" => stripslashes($description),
                "link"        => stripslashes($repinId),
                "is_video"    => null,
                "pin_id"      => $repinId,
            ],
            "context" => [],
        ];

        $post = [
            "source_url" => "/pin/{$repinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_REPIN, $postString);
        $this->checkErrorInResponse($res);

        return $this->parsePinCreateResponse($res);
    }

    /**
     * Parses pin create response
     *
     * @param $response
     * @return bool
     */
    protected function parsePinCreateResponse($response)
    {
        if (isset($response['resource_response']['data']['id'])) {
            return $response['resource_response']['data']['id'];
        }

        return false;
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

        $dataJson = [
            "options" => [
                "id" => $pinId,
            ],
            "context" => new \stdClass(),
        ];

        $post = [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(UrlHelper::RESOURCE_DELETE_PIN, $postString);

        $this->checkErrorInResponse($res);

        return $res ? true : false;
    }

    /**
     * Iterate through results of Api function call. By
     * default generator will return all pagination results.
     * To limit result batches, set $batchesLimit.
     *
     * @param string $function
     * @param array  $params
     * @param int    $batchesLimit
     * @return \Generator
     */
    protected function getPaginatedData($function, $params, $batchesLimit = 0)
    {
        $batchesNum = 0;
        do {

            if ($batchesLimit && $batchesNum >= $batchesLimit) {
                break;
            }

            $items = [];
            $res   = call_user_func_array([$this, $function], $params);

            if (isset($res['data']) && ! empty($res['data'])) {

                if (isset($res['data'][0]['type']) && $res['data'][0]['type'] == 'module') {
                    array_shift($res['data']);
                }
                $items = $res['data'];
            }

            if (isset($res['bookmarks'])) {
                $params['bookmarks'] = $res['bookmarks'];
            }

            if (empty($items)) {
                return; }

            $batchesNum++;
            yield $items;


        } while (isset($res['data']) && ! empty($res['data']));

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
        return $this->getPaginatedData('getUserData',
            ['username'  => $username,
             'url'       => UrlHelper::RESOURCE_USER_FOLLOWERS,
             'sourceUrl' => "/$username/followers/"],
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
        return $this->getPaginatedData('getUserData',
            ['username'  => $username,
             'url'       => UrlHelper::RESOURCE_USER_FOLLOWING,
             'sourceUrl' => "/$username/following/"],
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
        return $this->getPaginatedData('getUserData',
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
        $modulePath = 'App(module=[object Object])';
        $options    = [
            "restrict"            => null,
            "scope"               => $scope,
            "constraint_string"   => null,
            "show_scope_selector" => true,
            "query"               => $query,
        ];
        $dataJson   = [
            "options" => $options,
            "context" => new \stdClass(),
        ];

        if (empty($bookmarks)) {
            $dataJson['module'] = [
                "name" => "SearchPage",
                "options" => $options,
            ];

            $url = UrlHelper::RESOURCE_SEARCH;

        } else {
            $options["bookmarks"] = $bookmarks;
            $url                  = UrlHelper::RESOURCE_SEARCH_WITH_PAGINATION;
        }

        $get = [
            "source_url"  => "/search/$scope/?q=" . $query,
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($modulePath),
        ];
        $url = $url . '?' . UrlHelper::buildRequestString($get);
        $res = $this->api->exec($url);

        if ($res === null) {
            return [];
        }

        return $this->parseSearchResponse($res, ! empty($bookmarks));
    }

    /**
     * Parses Pinterest search API response for data and bookmarks
     * for next pagination page
     *
     * @param string $res
     * @param bool   $bookmarksUsed
     * @return array
     */
    protected function parseSearchResponse($res, $bookmarksUsed)
    {
        if ($bookmarksUsed) {
            return $this->parseSimpledSearchResponse($res);
        } else {
            return $this->parseBookMarkedSearchResponse($res);
        }
    }


    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks
     *
     * @param $res
     * @return array
     */
    protected function parseSimpledSearchResponse($res)
    {
        if ( ! empty($res['resource_response']['data'])) {
            return [
                'data'      => $res['resource_response']['data'],
                'bookmarks' => $res['resource']['options']['bookmarks'],
            ];
        }

        return [];
    }

    /**
     * Parses Pinterest search API response
     * on request without bookmarks
     *
     * @param $res
     * @return array
     */
    protected function parseBookMarkedSearchResponse($res)
    {
        $bookmarks = [];
        if (isset($res['module']['tree']['resource']['options']['bookmarks'][0])) {
            $bookmarks = $res['module']['tree']['resource']['options']['bookmarks'][0];
        }

        if ( ! empty($res['module']['tree']['data']['results'])) {
            return ['data' => $res['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
        }

        return [];
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
        return $this->getPaginatedData('search',
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
        return $this->getPaginatedData('search',
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
        return $this->getPaginatedData('search',
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
        $dataJson = [

            "options" => [
                "field_set_key"               => "detailed",
                "fetch_visual_search_objects" => true,
                "id"                          => $pinId,
                "allow_stale"                 => true,
            ],
            "context" => new \StdClass(),
        ];

        $get = [
            "source_url" => "/pin/$pinId/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $url = UrlHelper::RESOURCE_PIN_INFO . '?' . UrlHelper::buildRequestString($get);
        $res = $this->api->exec($url);
        if ($res) {
            if (isset($res['resource_response']['data'])) {
                return $res['resource_response']['data'];
            }
        }

        return null;
    }

    /**
     * Follow board by boardID
     *
     * @param $boardId
     * @return bool
     */
    public function followBoard($boardId)
    {
        return $this->followMethodCall($boardId, "board_id", UrlHelper::RESOURCE_FOLLOW_BOARD);

    }

    /**
     * Unfollow board by boardID
     *
     * @param $boardId
     * @return bool
     */
    public function unFollowBoard($boardId)
    {
        return $this->followMethodCall($boardId, "board_id", UrlHelper::RESOURCE_UNFOLLOW_BOARD);

    }
}