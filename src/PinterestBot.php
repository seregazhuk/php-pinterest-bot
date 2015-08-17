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

    const URL_GET_ACCOUNT_NAME       = "https://www.pinterest.com/";
    const URL_LOGIN                  = "https://www.pinterest.com/resource/UserSessionResource/create/";
    const URL_GET_BOARDS             = "https://www.pinterest.com/resource/BoardPickerBoardsResource/get/";
    const URL_CREATE_PIN             = "https://www.pinterest.com/resource/PinResource/create/";
    const URL_REPIN                  = "https://www.pinterest.com/resource/RepinResource/create/";
    const URL_USER_FOLLOWERS         = "https://www.pinterest.com/resource/UserFollowersResource/get/";
    const URL_DELETE_PIN             = "https://www.pinterest.com/resource/PinResource/delete/";
    const URL_FOLLOW_USER            = 'https://www.pinterest.com/resource/UserFollowResource/create/';
    const URL_UNFOLLOW_USER          = "https://www.pinterest.com/resource/UserFollowResource/delete/";
    const URL_SEARCH                 = "https://www.pinterest.com/resource/BaseSearchResource/get/";
    const URL_SEARCH_WITH_PAGINATION = "https://www.pinterest.com/resource/SearchResource/get/";
    const URL_USER_INFO              = "https://www.pinterest.com/resource/UserResource/get/";
    const URL_USER_FOLLOWING         = "https://www.pinterest.com/resource/UserFollowingResource/get/";
    const URL_USER_PINS              = "https://www.pinterest.com/resource/UserPinsResource/get/";
    const URL_LIKE_PIN               = "https://www.pinterest.com/resource/PinLikeResource2/create/";
    const URL_UNLIKE_PIN             = "https://www.pinterest.com/resource/PinLikeResource2/delete/";
    const URL_COMMENT_PIN            = "https://www.pinterest.com/resource/PinCommentResource/create/";
    const URL_BASE                   = 'https://www.pinterest.com/';


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

        // Prepare the login data json
        $dataJson = [
            "options" => [
                "username_or_email" => $this->username,
                "password"          => $this->password,
            ],
            "context" => [],
        ];

        // And prepare the post data array
        $post = [
            "source_url"  => "/login/",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => "App()>LoginPage()>Login()>Button(class_name=primary, text=Log In, type=submit, size=large)",
        ];

        $postString = UrlHelper::buildRequestString($post);

        $res = $this->api->exec(self::URL_LOGIN,
            "https://www.pinterest.com/login/",
            $postString,
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

        $res = $this->api->exec(self::URL_GET_BOARDS . "?{$getString}",
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
        $res = $this->api->exec(self::URL_GET_ACCOUNT_NAME, self::URL_BASE);
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
        return $this->followUserMethodCall($userId, self::URL_FOLLOW_USER);
    }


    /**
     * Unfollow user by user_id
     *
     * @param integer $userId
     * @return bool
     */
    public function unFollowUser($userId)
    {
        return $this->followUserMethodCall($userId, self::URL_UNFOLLOW_USER);
    }

    /**
     * Executes api call for follow or unfollow user
     *
     * @param int    $userId
     * @param string $url
     * @return bool
     */
    protected function followUserMethodCall($userId, $url)
    {
        $this->checkLoggedIn();

        $dataJson = [
            "options" => [
                "user_id" => $userId,
            ],
            "context" => [],
        ];
        $post     = [
            "data" => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = UrlHelper::buildRequestString($post);

        $res = $this->api->exec($url,
            self::URL_BASE,
            $postString);

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
        $this->checkLoggedIn();

        $dataJson = [
            "options" => [
                "pin_id" => $pinId,
            ],
            "context" => [],
        ];

        $post = [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = URlHelper::buildRequestString($post);

        $res = $this->api->exec(self::URL_LIKE_PIN,
            self::URL_BASE . "pin/$pinId/",
            $postString);


        if ($res !== null && isset($res['resource_response'])) {
            return true;
        }

        return false;
    }


    /**
     * Removes your like from pin with current ID
     *
     * @param integer $pinId
     * @return bool
     */
    public function unLikePin($pinId)
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

        $res = $this->api->exec(self::URL_UNLIKE_PIN,
            self::URL_BASE . "pin/$pinId/",
            $postString);


        if ($res !== null && isset($res['resource_response'])) {
            return true;
        }

        return false;
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

        $res = $this->api->exec(self::URL_COMMENT_PIN,
            self::URL_BASE . "pin/$pinId/",
            $postString
        );

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

        $res = $this->api->exec($url . '?' . $getString,
            self::URL_BASE . $username
        );


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
            self::URL_USER_INFO,
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
        $res        = $this->api->exec(self::URL_CREATE_PIN, self::URL_BASE, $postString);

        $this->checkErrorInResponse($res);

        if (isset($res['resource_response']['data']['id'])) {
            return $res['resource_response']['data']['id'];
        }

        return false;
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
        $res        = $this->api->exec(self::URL_REPIN, self::URL_BASE, $postString);
        $this->checkErrorInResponse($res);

        if (isset($res['resource_response']['data']['id'])) {
            return $res['resource_response']['data']['id'];
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
        $res        = $this->api->exec(self::URL_DELETE_PIN, self::URL_BASE, $postString);

        $this->checkErrorInResponse($res);
        if ($res) {
            return true;
        }

        return false;
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
                return;
            }

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
            ['username' => $username, 'url' => self::URL_USER_FOLLOWERS, 'sourceUrl' => "/$username/followers/"],
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
            ['username' => $username, 'url' => self::URL_USER_FOLLOWING, 'sourceUrl' => "/$username/following/"],
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
            ['username' => $username, 'url' => self::URL_USER_PINS, 'sourceUrl' => "/$username/pins/"],
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
        if (empty($bookmarks)) {
            $options  = [
                "restrict"            => null,
                "scope"               => $scope,
                "constraint_string"   => null,
                "show_scope_selector" => true,
                "query"               => $query,
            ];
            $dataJson = [
                "options" => $options,
                "context" => [],
                'module'  => [
                    "name"    => "SearchPage",
                    "options" => $options,
                ],
            ];


            $modulePath = 'App(module=[object Object])';
            // And prepare the post data array
            $get = [
                "source_url"  => "/search/pins/?q=" . $query,
                "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
                "module_path" => urlencode($modulePath),
            ];
            $url = self::URL_SEARCH . '?' . UrlHelper::buildRequestString($get);
        } else {
            $options  = [
                "layout"              => null,
                "places"              => false,
                "scope"               => $scope,
                "constraint_string"   => null,
                "show_scope_selector" => true,
                "bookmarks"           => $bookmarks,
                "query"               => $query,
            ];
            $dataJson = [
                "options" => $options,
                "context" => new \stdClass(),
            ];


            $modulePath = 'App(module=[object Object])';
            $get        = [
                "source_url"  => "/search/pins/?q=" . $query,
                "data"        => json_encode($dataJson),
                "module_path" => urlencode($modulePath),
            ];

            $url = self::URL_SEARCH_WITH_PAGINATION . '?' . UrlHelper::buildRequestString($get);
        }


        $res = $this->api->exec($url, self::URL_BASE);

        if (empty($bookmarks)) {
            if (isset($res['module']['tree']['resource']['options']['bookmarks'][0])) {
                $bookmarks = $res['module']['tree']['resource']['options']['bookmarks'][0];
            } else {
                $bookmarks = [];
            }

            if ($res === null) {
                return [];
            } else {
                if ( ! empty($res['module']['tree']['data']['results'])) {
                    return ['data' => $res['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
                } else {
                    return [];
                }
            }
        } else {

            if ( ! empty($res['resource_response']['data'])) {
                return [
                    'data'      => $res['resource_response']['data'],
                    'bookmarks' => $res['resource']['options']['bookmarks'],
                ];
            } else {
                return [];
            }
        }

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
}