<?php

namespace Pinterest;

use Pinterest\helpers\UrlHelper;
use Pinterest\helpers\CsrfHelper;

/**
 * Class PinterestBot
 *
 * @package Pinterest
 * @property $userAgent string
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
            $this->api->setLoggedIn(CsrfHelper::getCsrfToken($this->api->cookieJar));
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

        // OK!  We're ready!  Prepare the board get JSON
        $dataJson = array(
            "options" => array(
                "filter"        => "all",
                "field_set_key" => "board_picker",
            ),
            "context" => array(),
        );

        // And prepare the get data array
        $get = array(
            "source_url"  => "/pin/create/bookmarklet/?url=",
            "pinFave"     => "1",
            "description" => "",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
        );

        $getString = UrlHelper::buildRequestString($get);

        // Now set up the CURL call
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
        $this->checkLoggedIn();

        // OK!  We're ready!  Prepare the pin JSON
        $dataJson = [
            "options" => [
                "user_id" => $userId,
            ],
            "context" => [],
        ];

        // Set up the "module path" data
        $modulePath = "App()>UserProfilePage(resource=UserResource(username=jocleveland, invite_code=null))>UserProfileHeader
                        (resource=UserResource(username=jocleveland, invite_code=null))>UserFollowButton(followed=false, is_me
                        =false, unfollow_text=Unfollow, memo=[object Object], follow_ga_category=user_follow, unfollow_ga_category
                        =user_unfollow, disabled=false, color=primary, text=Follow, user_id=$userId, follow_text=Follow
                        , follow_class=primary)";

        // And prepare the post data array
        $post = [
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($modulePath),
        ];

        $postString = UrlHelper::buildRequestString($post);

        $res = $this->api->exec(self::URL_FOLLOW_USER,
            self::URL_BASE,
            $postString
        );

        if ($res === null) {
            return false;
        }

        return true;
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

        $dataJson = [
            "options" => [
                "user_id" => $userId,
            ],
            "context" => [],
        ];

        // Set up the "module path" data
        $modulePath = "App()>UserProfilePage(resource=UserResource(username=jocleveland, invite_code=null))>UserProfileHeader
                        (resource=UserResource(username=jocleveland, invite_code=null))>UserFollowButton(followed=true, is_me
                        =false, unfollow_text=Unfollow, memo=[object Object], follow_ga_category=user_follow, unfollow_ga_category
                        =user_unfollow, disabled=false, color=dim, text=Unfollow, user_id=$userId, follow_text=Follow
                        , follow_class=primary)";

        // And prepare the post data array
        $post = [
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($modulePath),
        ];


        $postString = UrlHelper::buildRequestString($post);

        $res = $this->api->exec(self::URL_UNFOLLOW_USER,
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

        $dataJson = array(
            "options" => array(
                "pin_id" => $pinId,
            ),
            "context" => array(),
        );

        // Set up the "module path" data
        $module_path = "App()>Closeup(resource=PinResource(fetch_visual_search_objects=true, id={$pinId}))>PinActionBar
                        (resource=PinResource(fetch_visual_search_objects=true, id={$pinId}, allow_stale=true))>PinLikeButton
                        (class_name=like pinActionBarButton, liked=false, size=medium, has_icon=true, pin_id={$pinId}
                        , show_text=true, text=Like)";

        // And prepare the post data array
        $post = array(
            "source_url"  => "/pin/{$pinId}/",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($module_path),
        );

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

        $dataJson = array(
            "options" => array(
                "pin_id" => $pinId,
            ),
            "context" => array(),
        );

        // Set up the "module path" data
        $module_path = "App>Closeup>PinActionBar>PinLikeButton(liked=true, has_icon=false, pin_id=$pinId, class_name
						=like leftRounded pinActionBarButton, text=Delete Лайк, show_text=true, size=medium";

        // And prepare the post data array
        $post = array(
            "source_url"  => "/pin/{$pinId}/",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($module_path),
        );

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

        $dataJson = array(
            "options" => array(
                "pin_id" => $pinId,
                "text"   => $text,
            ),
            "context" => array(),
        );

        // Set up the "module path" data
        $module_path = "App()>Closeup(resource=PinResource(fetch_visual_search_objects=true, id={$pinId}))>CloseupContent
                        (resource=PinResource(fetch_visual_search_objects=true, id={$pinId}))>Pin(resource=PinResource
                        (id={$pinId}))>PinCommentsPage(resource=PinCommentListResource(pin_id={$pinId}, page_size
                        =5))>PinDescriptionComment(content=null, show_comment_form=true, view_type=detailed, pin_id={$pinId},
                        is_description=false)";

        // And prepare the post data array
        $post = array(
            "source_url"  => "/pin/{$pinId}/",
            "data"        => json_encode($dataJson, JSON_FORCE_OBJECT),
            "module_path" => urlencode($module_path),
        );

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

        if ( ! $username) {
            $username = $this->username;
        }

        $dataJson = [
            "options" => [
                "username" => $username,
            ],
            "context" => new \stdClass(),
        ];

        if ( ! empty($bookmarks)) {
            $dataJson["options"]["bookmarks"] = $bookmarks;
        }

        // Set up the "module path" data
        $modulePath = "UserProfilePage(resource=UserResource(username=ç))";

        // And prepare the post data array
        $get = [
            "source_url"  => $sourceUrl,
            "data"        => json_encode($dataJson, true),
            "module_path" => urlencode($modulePath),
        ];

        $getString = UrlHelper::buildRequestString($get);

        $res = $this->api->exec($url . '?' . $getString,
            self::URL_BASE . $username
        );


        if ($this->checkErrorInResponse($res)) {
            return [];
        }

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
     * @return bool
     */
    protected function checkErrorInResponse($response)
    {
        if (isset($response['api_error_code'])) {
            $this->lastApiErrorCode = $response['api_error_code'];
            $this->lastApiErrorMsg  = $response['message'];

            return true;
        }
        return false;
    }

    /**
     * Get user info
     * If username param is not specified, will
     * return info for logged user

     *
*@param string $username
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

        if ($this->checkErrorInResponse($res)) {
            return false;
        }

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

        $dataJson = array(
            "options" => array(
                "board_id"    => $boardId,
                "description" => stripslashes($description),
                "link"        => stripslashes($repinId),
                "is_video"    => null,
                "pin_id"      => $repinId,
            ),
            "context" => array(),
        );

        $post = array(
            "source_url" => "/pin/{$repinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        );

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(self::URL_REPIN, self::URL_BASE, $postString);

        if ($this->checkErrorInResponse($res)) {
            return false;
        }

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

        // And prepare the post data array
        $post = [
            "source_url" => "/pin/{$pinId}/",
            "data"       => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];

        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->api->exec(self::URL_DELETE_PIN, self::URL_BASE, $postString);

        if ($this->checkErrorInResponse($res)) {
            return false;
        }


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
*@param string $function
     * @param array  $params
     * @param int $batchesLimit
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
* @param string   $username
     * @param int $batchesLimit
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
    public function searchPinners($query, $batchesLimit)
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