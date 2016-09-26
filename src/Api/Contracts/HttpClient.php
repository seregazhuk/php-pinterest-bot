<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface HttpClient
{
    const COOKIE_PREFIX = 'pinterest_cookie_';

    /**
     * Get curl errors.
     *
     * @return string
     */
    public function getErrors();

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
     * @return $this
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
}
