<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\FileHelper;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Contracts\HttpInterface;
use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Contracts\RequestInterface;

/**
 * Class Request.
 *
 * @property resource $ch
 * @property bool     $loggedIn
 * @property string   $userAgent
 * @property string   $csrfToken
 * @property string   $cookieJar
 */
class Request implements RequestInterface
{
    const COOKIE_NAME = 'pinterest_cookie';

    protected $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';
    /**
     * @var HttpInterface
     */
    protected $http;
    protected $loggedIn;
    protected $cookieJar;
    protected $options;

    /**
     *
     * @var string
     */
    protected $filePathToUpload;

    public $csrfToken = '';

    /**
     * Common headers needed for every query.
     *
     * @var array
     */
    protected $requestHeaders = [
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.5',
        'DNT: 1',
        'Host: nl.pinterest.com',
        'X-Pinterest-AppState: active',
        'X-NEW-APP: 1',
        'X-APP-VERSION: 04cf8cc',
        'X-Requested-With: XMLHttpRequest',
    ];

    /**
     * @var string
     */
    protected $postFileData;

    /**
     * @param HttpInterface $http
     */
    public function __construct(HttpInterface $http)
    {
        $this->http = $http;
        $this->cookieJar = tempnam(sys_get_temp_dir(), self::COOKIE_NAME);
    }

    /**
     * Executes api call for follow or unfollow user.
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     *
     * @return array
     */
    public function followMethodCall($entityId, $entityName, $url)
    {
        $dataJson = [
            'options' => [
                $entityName => (string)$entityId,
            ],
            'context' => [],
        ];

        if ($entityName == 'interest_id') {
            $dataJson['options']['interest_list'] = 'favorited';
        }

        $post = ['data' => json_encode($dataJson, JSON_FORCE_OBJECT)];
        $postString = UrlHelper::buildRequestString($post);

        return $this->exec($url, $postString);
    }

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
     * @return array
     */
    public function exec($resourceUrl, $postString = '')
    {
        $url = UrlHelper::buildApiUrl($resourceUrl);        
        $this->makeHttpOptions($postString);
        $res = $this->http->execute($url, $this->options);

        $this->filePathToUpload = null;
        return json_decode($res, true);
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
    public function setLoggedIn()
    {
        $this->csrfToken = CsrfHelper::getTokenFromFile($this->cookieJar);
        if (empty($this->csrfToken)) {
            throw new AuthException('Cannot parse token from cookies.');
        }
        $this->loggedIn = true;
        return $this;
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
     * @param string $sourceUrl
     * @param array  $bookmarks
     *
     * @return string
     */
    public static function createQuery(array $data = [], $sourceUrl = '/', $bookmarks = [])
    {
        $request = self::createRequestData($data, $sourceUrl, $bookmarks);

        return UrlHelper::buildRequestString($request);
    }

    /**
     * @param array|object $data
     * @param string|null  $sourceUrl
     * @param array        $bookmarks
     *
     * @return array
     */
    public static function createRequestData(array $data = [], $sourceUrl = '/', $bookmarks = [])
    {
        if (empty($data)) {
            $data = ['options' => []];
        }

        if (!empty($bookmarks)) {
            $data['options']['bookmarks'] = $bookmarks;
        }

        $data['context'] = new \stdClass();

        return [
            'source_url' => $sourceUrl,
            'data'       => json_encode($data),
        ];
    }

    /**
     * @return array
     */
    protected function getDefaultHttpHeaders()
    {
        return array_merge(
            $this->requestHeaders, $this->getContentTypeHeader(), ['X-CSRFToken: ' . $this->csrfToken]
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
        if ($this->filePathToUpload) {
            $delimiter = '-------------' . uniqid();
            $this->buildFilePostData($delimiter);

            return [
                'Content-Type: multipart/form-data; boundary=' . $delimiter,
                'Content-Length: ' . strlen($this->postFileData)
            ];
        }

        return ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8;'];
    }

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
}
