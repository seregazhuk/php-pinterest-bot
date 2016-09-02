<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Helpers\CsrfParser;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class CurlAdapter.
 */
class CurlHttpClient implements HttpClient
{
    public $cookieName = 'pinterest_cookie';

    /**
     * Custom CURL options for requests.
     *
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';

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

    public function __construct()
    {
        $this->cookieJar = tempnam(sys_get_temp_dir(), $this->cookieName);
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
     * @return null|string
     */
    public function getToken()
    {
        return CsrfParser::getTokenFromFile($this->cookieJar);
    }

    /**
     * @param string $userAgent
     * @return $this
     */
    public function setUserAgent($userAgent)
    {
        if ($userAgent !== null) {
            $this->userAgent = $userAgent;
        }

        return $this;
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

        curl_setopt_array(
            $this->curl,
            $this->makeHttpOptions($postString)
        );

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultHttpOptions()
    {
        return [
            CURLOPT_USERAGENT      => $this->userAgent,
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
        $options = $this->getDefaultHttpOptions();

        if (!empty($postString)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postString;
        }

        // Override default options with custom
        if (!empty($this->options)) {
            foreach ($this->options as $option => $value) {
                $options[$option] = $value;
            }
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
}
