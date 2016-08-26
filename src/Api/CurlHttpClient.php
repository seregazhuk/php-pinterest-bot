<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

/**
 * Class CurlAdapter.
 */
class CurlHttpClient implements HttpClient
{
    const COOKIE_NAME = 'pinterest_cookie';

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';

    /**
     * @var array
     */
    protected $headers;

    /**
     * Contains the curl instance.
     *
     * @var resource
     */
    protected $curl;
    protected $cookieJar;

    public function __construct()
    {
        $this->cookieJar = tempnam(sys_get_temp_dir(), self::COOKIE_NAME);
    }

    /**
     * Executes curl request.
     *
     * @param string $url
     * @param string $postString
     * @param array $headers
     * @return string
     */
    public function execute($url, $postString, array $headers = [])
    {
        $this->headers = $headers;
        $this->init($url)->setOptions($postString);

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
        return CsrfHelper::getTokenFromFile($this->cookieJar);
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
     * Initializes curl resource.
     *
     * @param string $url
     *
     * @return $this
     */
    protected function init($url)
    {
        $this->curl = curl_init($url);

        return $this;
    }

    /**
     * Sets multiple options at the same time.
     *
     * @param string $postString
     * @return static
     */
    protected function setOptions($postString)
    {
        $this->makeHttpOptions($postString);

        curl_setopt_array($this->curl, $this->options);

        return $this;
    }

    /**
     * @return array
     */
    protected function setDefaultHttpOptions()
    {
        $this->options = [
            CURLOPT_USERAGENT      => $this->userAgent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING       => 'gzip,deflate',
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_REFERER        => UrlHelper::URL_BASE,
            CURLOPT_COOKIEFILE     => $this->cookieJar,
            CURLOPT_COOKIEJAR      => $this->cookieJar,
        ];
    }

    /**
     * Adds necessary curl options for query.
     *
     * @param string $postString POST query string
     *
     * @return $this
     */
    protected function makeHttpOptions($postString = '')
    {
        $this->setDefaultHttpOptions();

        if (!empty($postString)) {
            $this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_POSTFIELDS] = $postString;
        }

        return $this;
    }
}
