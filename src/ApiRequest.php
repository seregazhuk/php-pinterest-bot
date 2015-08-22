<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\helpers\CsrfHelper;
use seregazhuk\PinterestBot\helpers\UrlHelper;

/**
 * Class ApiRequest
 *
 * @package Pinterest
 * @property string   $cookieJar
 * @property string   $cookePath
 * @property resource $ch
 * @property array    $options
 * @property string   $csrfToken
 * @property bool     $loggedIn
 * @property string   $useragent
 */
class ApiRequest implements ApiInterface
{
    protected $cookieJar;
    public $cookiePath;

    protected $curl;
    protected $options;
    protected $csrfToken = "";
    protected $loggedIn;

    protected $useragent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';

    const COOKIE_NAME = 'pinterest_cookie';

    const INTEREST_ENTITY_ID = 'interest_id';
    const BOARD_ENTITY_ID    = 'board_id';
    const PINNER_ENTITY_ID   = 'user_id';
    const DEFAULT_CSRFTOKEN = '1234';

    /**
     * @param string      $useragent
     * @param null|string $cookiePath
     */
    public function __construct($useragent = "", $cookiePath = null)
    {
        if ( ! empty($useragent)) {
            $this->useragent = $useragent;
        }
        $this->cookiePath = $cookiePath;
        $this->cookieJarInit();
    }

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
     * Adds necessary curl options for query
     *
     * @param string    $postString        POST query string
     */
    public function setCurlOptions($postString = "")
    {

        $this->options = [
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $this->addHeadersCurlOptions();

        if ( ! empty($postString)) {
            $this->addPostCurlOption($postString);
        }

        $this->addCookieCurlOption();
        curl_setopt_array($this->curl, $this->options);
    }

    /**
     */
    protected function addHeadersCurlOptions()
    {
        $headers = $this->requestHeaders;
        $headers[] = 'X-CSRFToken: ' . $this->csrfToken;
        if ($this->csrfToken == self::DEFAULT_CSRFTOKEN) {
            $this->options[CURLOPT_REFERER] = UrlHelper::LOGIN_REF_URL;
            $headers[]                      = 'Cookie: csrftoken=1234;';
        }
        $this->options[CURLOPT_HTTPHEADER] = $headers;
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
     * Adds Post Query to curl request
     *
     * @param string $postString
     */
    protected function addPostCurlOption($postString)
    {
        $this->options[CURLOPT_POST]       = true;
        $this->options[CURLOPT_POSTFIELDS] = $postString;
    }

    /**
     * Executes api call to pinterest
     *
     * @param                  $resourceUrl
     * @param string           $postString
     * @return array
     */
    public function exec($resourceUrl, $postString = "")
    {
        $url = UrlHelper::buildApiUrl($resourceUrl);
        $this->curl = curl_init($url);
        $this->setCurlOptions($postString);
        $res = curl_exec($this->curl);
        curl_close($this->curl);
        return json_decode($res, true);
    }


    /**
     * Adds cookies to curl requests
     */
    protected function addCookieCurlOption()
    {
        if ($this->csrfToken != self::DEFAULT_CSRFTOKEN) {
            $this->options[CURLOPT_COOKIEFILE] = $this->cookieJar;
        } else {
            $this->options[CURLOPT_COOKIEJAR] = $this->cookieJar;
        }
    }


    /**
     * Check if cookies were saved before and load them
     */
    protected function cookieJarInit()
    {
        if (isset($this->cookiePath)) {
            // If the given cookie path exists, then let's assume
            // we're already logged in
            $this->cookieJar = $this->cookiePath;
            if (file_exists($this->cookieJar)) {

                // Set up our logged-in state
                $this->csrfToken = CsrfHelper::getCsrfToken($this->cookieJar);
                $this->loggedIn  = true;
            }

        } else {
            $this->cookieJar = tempnam(sys_get_temp_dir(), self::COOKIE_NAME);
        }
    }

    /**
     * Checks if current api user is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * Mark api as logged
     *
     * @param string $csrfToken Pinterest security token.
     */
    public function setLoggedIn($csrfToken)
    {
        $this->csrfToken = $csrfToken;
        $this->loggedIn  = true;
    }

    /**
     * Get requests cookieJar
     *
     * @return mixed
     */
    public function getCookieJar()
    {
        return $this->cookieJar;
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
        $dataJson   = [
            "options" => [
                $entityName => $entityId,
            ],
            "context" => [],
        ];

        if ($entityName == self::INTEREST_ENTITY_ID) {
            $dataJson["options"]["interest_list"] = "favorited";
        }

        $post       = [
            "data" => json_encode($dataJson, JSON_FORCE_OBJECT),
        ];
        $postString = UrlHelper::buildRequestString($post);
        $res        = $this->exec($url, $postString);

        if ($res === null) {
            return false;
        }

        return true;
    }

}