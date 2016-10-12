<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

/**
 * Class CurlAdapter.
 */
class CurlHttpClient implements HttpClient
{
    /**
     * Custom CURL options for requests.
     *
     * @var array
     */
    protected $options = [
        CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0'
    ];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Contains the curl instance.
     *
     * @var resource
     */
    protected $curl;

    /**
     * @var string
     */
    protected $cookieJar;

    /**
     * Cookies container
     *
     * @var Cookies
     */
    protected $cookies;

    /**
     * @var string
     */
    protected $currentUrl;

    /**
     * @var string
     */
    protected $cookiesPath;

    public function __construct(Cookies $cookies)
    {
        $this->cookies = $cookies;
    }

    /**
     * Load cookies for specified username
     *
     * @param string $username
     * @return HttpClient
     */
    public function loadCookies($username = '')
    {
        return $this->initCookieJar($username)
            ->fillCookies();
    }

    /**
     * Executes curl request.
     *
     * @param string $url
     * @param string $postString
     * @param array $headers
     * @return string
     */
    public function execute($url, $postString = '', array $headers = [])
    {
        return $this
            ->init($url, $postString, $headers)
            ->callCurl();
    }

    /**
     * @return mixed
     */
    protected function callCurl()
    {
        $res = curl_exec($this->curl);

        $this->currentUrl = curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL);

        curl_close($this->curl);

        $this->fillCookies();

        return $res;
    }

    /**
     * Initializes curl resource with options.
     *
     * @param string $url
     * @param string $postString
     * @param array $headers
     * @return $this
     */
    protected function init($url, $postString, $headers)
    {
        $this->headers = $headers;

        $this->curl = curl_init($url);

        if (empty($this->cookieJar)) {
            $this->loadCookies();
        }

        curl_setopt_array($this->curl, $this->makeHttpOptions($postString));

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultHttpOptions()
    {
        return [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => 'gzip,deflate',
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_REFERER        => UrlBuilder::URL_BASE,
            CURLOPT_COOKIEFILE     => $this->cookieJar,
            CURLOPT_COOKIEJAR      => $this->cookieJar,
        ];
    }

    /**
     * Adds necessary curl options for query.
     *
     * @param string $postString POST query string
     *
     * @return array
     */
    protected function makeHttpOptions($postString = '')
    {
        // Union custom Curl options and default.
        $options = array_replace(
            $this->options,
            $this->getDefaultHttpOptions()
        );

        if (!empty($postString)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postString;
        }

        return $options;
    }

    /**
     * Set custom Curl options to override default
     *
     * @param array $options
     * @return CurlHttpClient
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get a cookie value by name
     *
     * @param $name
     * @return mixed
     */
    public function cookie($name)
    {
        return $this->cookies->get($name);
    }

    /**
     * Get all cookies
     *
     * @return array
     */
    public function cookies()
    {
        return $this->cookies->all();
    }

    /**
     * Set directory to store all cookie files.
     * @param string $path
     * @return $this
     */
    public function setCookiesPath($path)
    {
        $this->cookiesPath = $path;

        return $this;
    }

    /**
     * Init cookie file for a specified username. If username is empty we use
     * common cookie file for all sessions. If file does not exist it will
     * be created in system temp directory.
     *
     * @param $username
     * @return $this
     */
    protected function initCookieJar($username = '')
    {
        $this->cookieJar = $this->initCookieFile($username);

        return $this;
    }

    /**
     * Returns cookie file name by username. If username is empty we use a
     * random cookie name, to be sure we have different cookies
     * in parallel sessions.
     *
     * @param string $username
     * @return string
     */
    protected function initCookieFile($username)
    {
        if(empty($username)) {
            return tempnam($this->getCookiesPath(), self::COOKIE_PREFIX);
        }

        $cookieName = self::COOKIE_PREFIX . $username;
        $cookieFilePath = $this->getCookiesPath() . DIRECTORY_SEPARATOR . $cookieName;

        if (!file_exists($cookieFilePath)) {
            touch($cookieFilePath);
        }

        return $cookieFilePath;
    }

    /**
     * @return string
     */
    public function getCookiesPath()
    {
        return $this->cookiesPath ? : sys_get_temp_dir();
    }

    /**
     * @return $this
     */
    protected function fillCookies()
    {
        $this->cookies->fill($this->cookieJar);

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }
}