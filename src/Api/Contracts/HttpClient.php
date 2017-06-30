<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface HttpClient
{
    const COOKIE_PREFIX = 'pinterest_bot_cookie';

    /**
     * Executes curl request.
     *
     * @param string $url
     * @param string $postString
     * @param array $headers
     * @return string
     */
    public function execute($url, $postString = '', array $headers = []);

    /**
     * Set custom Curl options to override default
     *
     * @param array $options
     * @return HttpClient
     */
    public function setOptions(array $options);

    /**
     * @param $name
     * @return mixed
     */
    public function cookie($name);

    /**
     * @return array
     */
    public function cookies();

    /**
     * Load cookies for specified username
     *
     * @param string $username
     * @return HttpClient
     */
    public function loadCookies($username = '');

    /**
     * Returns current url after all redirects
     * @return string
     */
    public function getCurrentUrl();

    /**
     * Set directory to store all cookie files.
     * @param string $path
     * @return HttpClient
     */
    public function setCookiesPath($path);

    /**
     * @return string
     */
    public function getCookiesPath();

    /**
     * @return HttpClient
     */
    public function removeCookies();

    /**
     * @param string $host '192.168.1.1'
     * @param string $port '12345'
     * @param string $auth Authentication string: 'username:password'
     * @param string $type HTTP|SOCKS
     * @return HttpClient
     */
    public function useProxy($host, $port, $auth = null, $type = null);

    /**
     * @param string $host
     * @param string $port
     * @param null $auth
     * @return HttpClient
     */
    public function useSocksProxy($host, $port, $auth = null);

    /**
     * @return HttpClient
     */
    public function dontUseProxy();

    /**
     * @return bool
     */
    public function usesProxy();
}
