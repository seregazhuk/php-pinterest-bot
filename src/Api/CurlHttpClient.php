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
        $this->initCookieJar($username);
        $this->cookies->fill($this->cookieJar);

        return $this;
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
        $this->headers = $headers;

        $this->init($url, $postString);

        $res = curl_exec($this->curl);
        $this->close();

        $this->cookies->fill($this->cookieJar);

        return $res;
    }

    /**
     * Get curl errors.
     *
     * @return string
     */
    public function getErrors()
    {
        return curl_error($this->curl);
    }

    /**
     * Close the curl resource.
     *
     * @return void
     */
    protected function close()
    {
        curl_close($this->curl);
    }

    /**
     * Initializes curl resource with options.
     *
     * @param string $url
     * @param $postString
     * @return $this
     */
    protected function init($url, $postString)
    {
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
     * Init cookie file for a specified username. If username is empty we use
     * common cookie file for all sessions. If file does not exist it will
     * be created in system temp directory.
     *
     * @param $username
     * @return $this
     */
    protected function initCookieJar($username = '')
    {
        $cookieFilePath = $this->getCookieFilePath($username);

        $this->cookieJar = $cookieFilePath;

        return $this;
    }

    /**
     * Return cookie file name by username. If username is empty we use a
     * random cookie name, to be sure we have different cookies
     * in parallel sessions.
     *
     * @param string $username
     * @return string
     */
    protected function getCookieFilePath($username)
    {
        if(empty($username)) {
            return tempnam(sys_get_temp_dir(), 'printerest_cookie_');
        }

        $cookieName = 'printerest_cookie_' . $username;
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $cookieName;
    }
}