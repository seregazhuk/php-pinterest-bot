<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Contracts\Http;
use seregazhuk\PinterestBot\Helpers\FileHelper;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Exceptions\AuthException;

/**
 * Class Request.
 *
 * @property resource $ch
 * @property bool     $loggedIn
 * @property string   $userAgent
 * @property string   $csrfToken
 * @property string   $cookieJar
 */
class Request
{
    const COOKIE_NAME = 'pinterest_cookie';

    /**
     * @var string
     */
    protected $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var bool
     */
    protected $loggedIn;

    /**
     * @var string
     */
    protected $cookieJar;

    /**
     * @var array
     */
    protected $options;

    /**
     *
     * @var string
     */
    protected $filePathToUpload;

    /**
     * @var string
     */
    protected $csrfToken = '';

    /**
     * @var string
     */
    protected $postFileData;

    /**
     * @var array|null
     */
    protected $lastError;


    /**
     * Common headers needed for every query.
     *
     * @var array
     */
    protected $requestHeaders = [
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.5',
        'DNT: 1',
        'X-Pinterest-AppState: active',
        'X-NEW-APP: 1',
        'X-APP-VERSION: 04cf8cc',
        'X-Requested-With: XMLHttpRequest',
    ];

    /**
     * @param Http $http
     */
    public function __construct(Http $http)
    {
        $this->http = $http;
        $this->loggedIn = false;
        $this->cookieJar = tempnam(sys_get_temp_dir(), self::COOKIE_NAME);
    }

    /**
     * @param string $pathToFile
     * @param string $url
     * @return array
     * @throws InvalidRequestException
     */
    public function upload($pathToFile, $url)
    {
        $this->filePathToUpload = $pathToFile;
        return $this->exec($url)->getResponseData();
    }

    /**
     * Executes request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param string $postString
     *
     * @return Response
     */
    public function exec($resourceUrl, $postString = '')
    {
        $url = UrlHelper::buildApiUrl($resourceUrl);
        $this->makeHttpOptions($postString);

        $result = $this
            ->http
            ->execute($url, $this->options);

        return $this->processResponse($result);
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

        if ($this->csrfToken == CsrfHelper::DEFAULT_TOKEN) {
            $this->options = $this->addDefaultCsrfInfo($this->options);
        }

        if (!empty($postString) || $this->filePathToUpload) {
            $this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_POSTFIELDS] = $this->filePathToUpload ? $this->postFileData : $postString;
        }

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
            CURLOPT_HTTPHEADER     => $this->getDefaultHttpHeaders(),
            CURLOPT_REFERER        => UrlHelper::URL_BASE,
            CURLOPT_COOKIEFILE     => $this->cookieJar,
            CURLOPT_COOKIEJAR      => $this->cookieJar,
        ];
    }

    /**
     * @param array $options
     *
     * @return mixed
     */
    protected function addDefaultCsrfInfo($options)
    {
        $options[CURLOPT_REFERER] = UrlHelper::URL_BASE;
        $options[CURLOPT_HTTPHEADER][] = CsrfHelper::getDefaultCookie();

        return $options;
    }
    
    /**
     * Clear token information.
     *
     * @return $this
     */
    public function clearToken()
    {
        $this->csrfToken = CsrfHelper::DEFAULT_TOKEN;

        return $this;
    }

    /**
     * Mark api as logged.
     * @return $this
     * @throws AuthException
     */
    public function login()
    {
        $this->setTokenFromCookies();
        $this->loggedIn = true;
    }

    public function logout()
    {
        $this->clearToken();
        $this->loggedIn = false;
    }

    /**
     * Get log status.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * @param $userAgent
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
     * Create request string.
     *
     * @param array  $data
     * @param array  $bookmarks
     *
     * @return string
     */
    public static function createQuery(array $data = [], $bookmarks = [])
    {
        $data = ['options' => $data];
        $request = self::createRequestData($data, $bookmarks);

        return UrlHelper::buildRequestString($request);
    }

    /**
     * @param array|object $data
     * @param array        $bookmarks
     *
     * @return array
     */
    public static function createRequestData(array $data = [], $bookmarks = [])
    {
        if (empty($data)) {
            $data = ['options' => []];
        }

        if (!empty($bookmarks)) {
            $data['options']['bookmarks'] = $bookmarks;
        }

        $data['context'] = new \stdClass();

        return [
            'source_url' => '',
            'data'       => json_encode($data),
        ];
    }

    public function setTokenFromCookies()
    {
        $this->csrfToken = CsrfHelper::getTokenFromFile($this->cookieJar);
        if (empty($this->csrfToken)) {
            throw new AuthException('Cannot parse token from cookies.');
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultHttpHeaders()
    {
        return array_merge(
            $this->requestHeaders, $this->getContentTypeHeader(), [
                'Host: ' . UrlHelper::HOST,
                'X-CSRFToken: ' . $this->csrfToken
            ]
        );
    }

    /**
     * If we are uploading file, we should build boundary form data. Otherwise
     * it is simple urlencoded form.
     *
     * @return array
     */
    protected function getContentTypeHeader()
    {
        return $this->filePathToUpload ?
            $this->makeHeadersForUpload() :
            ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8;'];
    }

    /**
     * @param string $delimiter
     * @return $this
     */
    protected function buildFilePostData($delimiter)
    {
        $data = "--$delimiter\r\n";
        $data .= 'Content-Disposition: form-data; name="img"; filename="' . basename($this->filePathToUpload) . '"' . "\r\n";
        $data .= 'Content-Type: ' . FileHelper::getMimeType($this->filePathToUpload) . "\r\n\r\n";
        $data .= file_get_contents($this->filePathToUpload) . "\r\n";
        $data .= "--$delimiter--\r\n";

        $this->postFileData = $data;

        return $this;
    }

    protected function makeHeadersForUpload()
    {
        $delimiter = '-------------' . uniqid();
        $this->buildFilePostData($delimiter);

        return [
            'Content-Type: multipart/form-data; boundary=' . $delimiter,
            'Content-Length: ' . strlen($this->postFileData)
        ];
    }

    /**
     * @param array|null $res
     * @return Response
     */
    protected function processResponse($res)
    {
        $this->filePathToUpload = null;
        $this->lastError = null;

        $response = new Response(json_decode($res, true));
        $this->lastError = $response->getLastError();

        return $response;
    }

    /**
     * @return array|null
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}
