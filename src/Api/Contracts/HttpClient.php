<?php

namespace seregazhuk\PinterestBot\Api\Contracts;

interface HttpClient
{
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
     * Returns csrf token from cookies.
     *
     * @return string|null
     */
    public function getToken();

    /**
     * Set custom Curl options to override default
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);
}
