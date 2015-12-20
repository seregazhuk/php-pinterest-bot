<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\Helpers\ResponseHelper;
use seregazhuk\PinterestBot\Interfaces\HttpInterface;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\SearchHelper;
use seregazhuk\PinterestBot\Interfaces\RequestInterface;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Helpers\PaginationHelper;

/**
 * Class Request
 *
 * @package Pinterest
 * @property resource $ch
 * @property bool     $loggedIn
 * @property string   $useragent
 * @property int      $lastApiError
 * @property string   $lastApiErrorMsg
 * @property string   $csrfToken
 * @property string   $cookieJar
 */
class Request implements RequestInterface
{
    const INTEREST_ENTITY_ID = 'interest_id';
    const BOARD_ENTITY_ID    = 'board_id';
    const PINNER_ENTITY_ID   = 'user_id';

    const SEARCH_PINS_SCOPE    = 'pins';
    const SEARCH_PEOPLE_SCOPE  = 'people';
    const SEARCH_BOARDS_SCOPES = 'boards';

    const DEFAULT_CSRFTOKEN = '1234';

    protected $useragent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';
    const COOKIE_NAME = 'pinterest_cookie';
    /**
     * @var Http
     */
    protected $http;
    protected $loggedIn;
    protected $cookieJar;

    public    $csrfToken = "";
    protected $lastApiError;

    /**
     * Common headers needed for every query
     *
     * @var array
     */
    protected $requestHeaders = [
        'Host: www.pinterest.com',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.5',
        'DNT: 1',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'X-Pinterest-AppState: active',
        'X-NEW-APP: 1',
        'X-APP-VERSION: 04cf8cc',
        'X-Requested-With: XMLHttpRequest',
    ];

    /**
     * @param HttpInterface $http
     */
    public function __construct(HttpInterface $http)
    {
        $this->http = $http;
        $this->cookieJar = self::COOKIE_NAME;

        if (file_exists($this->cookieJar)) {
            $this->setLoggedIn();
        }
    }

    /**
     * Executes api call for follow or unfollow user
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     * @return bool
     */
    public function followMethodCall($entityId, $entityName, $url)
    {
        $dataJson = [
            "options" => [
                $entityName => $entityId,
            ],
            "context" => [],
        ];

        if ($entityName == self::INTEREST_ENTITY_ID) {
            $dataJson["options"]["interest_list"] = "favorited";
        }

        $post = [
            "data" => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = UrlHelper::buildRequestString($post);
        return $this->exec($url, $postString);
    }

    /**
     * Checks if bot is logged in
     *
     * @throws \LogicException if is not logged in
     * @return bool
     */
    public function checkLoggedIn()
    {
        if ( ! $this->loggedIn) {
            throw new \LogicException("You must log in before.");
        }

        return true;
    }


    /**
     * Executes request to Pinterest API
     *
     * @param string $resourceUrl
     * @param string $postString
     * @return array
     */
    public function exec($resourceUrl, $postString = "")
    {
        $url = UrlHelper::buildApiUrl($resourceUrl);
        $options = $this->makeHttpOptions($postString);
        $this->http->init($url);
        $this->http->setOptions($options);

        $res = $this->http->execute();
        $this->http->close();

        return json_decode($res, true);
    }

    /**
     * Adds necessary curl options for query
     *
     * @param string $postString POST query string
     * @return array
     */
    protected function makeHttpOptions($postString = "")
    {
        $options = [
            CURLOPT_USERAGENT      => $this->useragent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => 'gzip,deflate',
        ];

        $headers = $this->requestHeaders;
        $headers[] = 'X-CSRFToken: '.$this->csrfToken;
        if ($this->csrfToken == self::DEFAULT_CSRFTOKEN) {
            $options[CURLOPT_REFERER] = UrlHelper::LOGIN_REF_URL;
            $headers[] = 'Cookie: csrftoken='.self::DEFAULT_CSRFTOKEN.';';
        } else {
            $options[CURLOPT_REFERER] = UrlHelper::URL_BASE;
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        if ( ! empty($postString)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postString;
        }

        $options[CURLOPT_COOKIEFILE] = $this->cookieJar;
        $options[CURLOPT_COOKIEJAR] = $this->cookieJar;

        return $options;
    }

    /**
     * Clear token information
     *
     * @return mixed
     */
    public function clearToken()
    {
        $this->csrfToken = self::DEFAULT_CSRFTOKEN;
    }

    /**
     * Mark api as logged
     */
    public function setLoggedIn()
    {
        $this->csrfToken = CsrfHelper::getTokenFromFile($this->cookieJar);
        if ( ! empty($this->csrfToken)) {
            $this->loggedIn = true;
        }
    }

    /**
     * Get log status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }
}
