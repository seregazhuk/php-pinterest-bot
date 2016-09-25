<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Helpers\FileHelper;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

/**
 * Class Request.
 */
class Request
{

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * @var bool
     */
    protected $loggedIn;

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
        'X-APP-VERSION: 4ed7174',
        'X-Requested-With: XMLHttpRequest',
    ];

    /**
     * @param HttpClient $http
     */
    public function __construct(HttpClient $http)
    {
        $this->httpClient = $http;
        $this->loggedIn = false;
    }

    /**
     * @param string $pathToFile
     * @param string $url
     * @return string
     * @throws InvalidRequest
     */
    public function upload($pathToFile, $url)
    {
        $this->filePathToUpload = $pathToFile;

        return $this->exec($url);
    }

    /**
     * Executes request to Pinterest API.
     *
     * @param string $resourceUrl
     * @param string $postString
     *
     * @return string
     */
    public function exec($resourceUrl, $postString = '')
    {
        $url = UrlBuilder::buildApiUrl($resourceUrl);
        $headers = $this->getHttpHeaders();
        $postString = $this->filePathToUpload ? $this->postFileData : $postString;

        $result = $this
            ->httpClient
            ->execute($url, $postString, $headers);

        $this->filePathToUpload = null;

        return $result;
    }

    /**
     * @return array
     */
    protected function getHttpHeaders()
    {
        $headers = $this->getDefaultHttpHeaders();
        if ($this->csrfToken == Cookies::DEFAULT_TOKEN) {
            $headers[] = 'Cookie: csrftoken=' . Cookies::DEFAULT_TOKEN . ';';
        }

        return $headers;
    }

    
    /**
     * Clear token information.
     *
     * @return $this
     */
    public function clearToken()
    {
        $this->csrfToken = Cookies::DEFAULT_TOKEN;

        return $this;
    }

    /**
     * Load cookies for this username and check if it was logged in.
     * @param string $username
     * @return bool
     */
    public function autoLogin($username)
    {
        $this->httpClient->loadCookies($username);

        if(!$this->httpClient->cookie('_auth')) {
            return false;
        }

        $this->login();

        return true;
    }

    /**
     * Mark client as logged.
     */
    public function login()
    {
        $this->setTokenFromCookies();

        if(!empty($this->csrfToken)) {
            $this->loggedIn = true;
        }
    }

    /**
     * Mark client as logged out.
     */
    public function logout()
    {
        $this->clearToken();
        $this->loggedIn = false;
    }

    /**
     * Get current auth status.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
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
        $request = self::createRequestData(
            ['options' => $data], $bookmarks
        );

        return UrlBuilder::buildRequestString($request);
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

    /**
     * Trying to get csrf token from cookies.
     *
     * @return $this
     */
    public function setTokenFromCookies()
    {
        $this->csrfToken = $this->httpClient->cookie('csrftoken');

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaultHttpHeaders()
    {
        return array_merge(
            $this->requestHeaders,
            $this->getContentTypeHeader(),
            [
                'Host: ' . UrlBuilder::HOST,
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

    /**
     * @return array
     */
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
     * @return array|null
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
