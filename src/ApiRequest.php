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

    protected $ch;
    protected $options;
    protected $csrfToken = "";
    protected $loggedIn;

    protected $useragent;

    const COOKIE_NAME = 'pinterest_cookie';

    /**
     * @param string      $useragent
     * @param null|string $cookiePath
     */
    public function __construct(
        $useragent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0',
        $cookiePath = null
    )
    {
        $this->useragent  = $useragent;
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
     * @param string    $referer           Referer Header
     * @param string    $postString        POST query string
     * @param array     $additionalHeaders Additional headers, needed for query
     * @param bool      $csrfToken         Flag to add csrfToken to headers
     * @param bool|true $cookeFileExists
     */
    public function setCurlOptions(
        $referer,
        $postString = "",
        $additionalHeaders = [],
        $csrfToken = true,
        $cookeFileExists = true
    ){
        $referer = $this->getReferer($referer);

        $this->options = [
            CURLOPT_REFERER        => $referer,
            CURLOPT_USERAGENT => $this->useragent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ];


        $this->addHeadersCurlOptions($additionalHeaders, $csrfToken);

        if ( ! empty($postString)) {
            $this->addPostCurlOption($postString);
        }

        $this->addCookieCurlOption($cookeFileExists);

        curl_setopt_array($this->ch, $this->options);
    }

    /**
     * @param array $additionalHeaders
     * @param bool  $csrfToken
     */
    protected function addHeadersCurlOptions($additionalHeaders = [], $csrfToken = true)
    {
        $headers = $this->requestHeaders;

        if ($csrfToken) {
            $headers[] = 'X-CSRFToken: ' . $this->csrfToken;
        }

        if ( ! empty($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        $this->options[CURLOPT_HTTPHEADER] = $headers;
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
     * @param                  $referer
     * @param array            $headers
     * @param bool|false       $csrfToken
     * @param bool|true        $cookieFileExists
     * @return array
     */
    public function exec(
        $resourceUrl,
        $postString = "",
        $referer = "",
        $headers = [],
        $csrfToken = true,
        $cookieFileExists = true
    ){
        $url = UrlHelper::buildApiUrl($resourceUrl);
        $this->ch = curl_init($url);
        $this->setCurlOptions($referer, $postString, $headers, $csrfToken, $cookieFileExists);
        $res = curl_exec($this->ch);
        curl_close($this->ch);

        return json_decode($res, true);
    }

    /**
     * Creates Pinterest api call referer
     *
     * @param string $referer
     * @return string
     */
    protected function getReferer($referer)
    {
        return UrlHelper::URL_BASE . $referer;
    }

    /**
     * Adds cookies to curl requests
     *
     * @param bool|true $cookeFileExists
     */
    protected function addCookieCurlOption($cookeFileExists = true)
    {
        if ($cookeFileExists) {
            $this->options[CURLOPT_COOKIEFILE] = $this->cookieJar;
        } else {
            $this->options[CURLOPT_COOKIEJAR] = $this->cookieJar;
        }
    }


    /**
     * Check if coockies were saved before and load them
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