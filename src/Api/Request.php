<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
use seregazhuk\PinterestBot\Contracts\HttpInterface;
use seregazhuk\PinterestBot\Contracts\RequestInterface;

/**
 * Class Request
 *
 * @package Pinterest
 * @property resource $ch
 * @property bool     $loggedIn
 * @property string   $userAgent
 * @property string   $csrfToken
 * @property string   $cookieJar
 */
class Request implements RequestInterface
{
    const INTEREST_ENTITY_ID = 'interest_id';
    const BOARD_ENTITY_ID = 'board_id';
    const COOKIE_NAME = 'pinterest_cookie';
    const PINNER_ENTITY_ID = 'user_id';

    protected $userAgent = 'Mozilla/5.0 (X11; Linux x86_64; rv:31.0) Gecko/20100101 Firefox/31.0';
    /**
     * @var HttpInterface
     */
    protected $http;
    protected $loggedIn;
    protected $cookieJar;

    public $csrfToken = "";

    /**
     * Common headers needed for every query
     *
     * @var array
     */
    protected $requestHeaders = [
        'Accept: application/json, text/javascript, */*; q=0.01',
        'Accept-Language: en-US,en;q=0.5',
        'DNT: 1',
        'Host: nl.pinterest.com',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'X-Pinterest-AppState: active',
        'X-NEW-APP: 1',
        'X-APP-VERSION: 04cf8cc',
        'X-Requested-With: XMLHttpRequest',
    ];

    /**
     * @param HttpInterface $http
     * @param string|null $userAgent
     */
    public function __construct(HttpInterface $http, $userAgent = null)
    {
        $this->http = $http;
        if ($userAgent !== null) {
            $this->userAgent = $userAgent;
        }
        $this->cookieJar = self::COOKIE_NAME;
    }

    /**
     * Executes api call for follow or unfollow user
     *
     * @param int    $entityId
     * @param string $entityName
     * @param string $url
     * @return array
     */
    public function followMethodCall($entityId, $entityName, $url)
    {
        $dataJson = [
            "options" => [
                $entityName => $entityId,
            ],
            "context" => [],
        ];

        if ($entityName == self::INTEREST_ENTITY_ID) {
            $dataJson["options"]["interest_list"] = "favorited";
        }

        $post = ["data" => json_encode($dataJson, JSON_FORCE_OBJECT)];
        $postString = UrlHelper::buildRequestString($post);

        return $this->exec($url, $postString);
    }

    /**
     * Checks if bot is logged in
     *
     * @throws \LogicException if is not logged in
     * @return bool
     */
    public function checkLoggedIn()
    {
        if ( ! $this->loggedIn) {
            throw new \LogicException("You must log in before.");
        }

        return true;
    }

    /**
     * Executes request to Pinterest API
     *
     * @param string $resourceUrl
     * @param string $postString
     * @return array
     */
    public function exec($resourceUrl, $postString = "")
    {
        $url = UrlHelper::buildApiUrl($resourceUrl);
        $options = $this->makeHttpOptions($postString);
        $this->http->init($url);
        $this->http->setOptions($options);
        $res = $this->http->execute();
        $this->http->close();

        return json_decode($res, true);
    }

    /**
     * Adds necessary curl options for query
     *
     * @param string $postString POST query string
     * @return array
     */
    protected function makeHttpOptions($postString = "")
    {
        $options = $this->getDefaultHttpOptions();

        if ($this->csrfToken == CsrfHelper::DEFAULT_TOKEN) {
            $options = $this->addDefaultCsrfInfo($options);
        }

        if ( ! empty($postString)) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $postString;
        }

        return $options;
    }

    /**
     * Clear token information
     */
    public function clearToken()
    {
        $this->csrfToken = CsrfHelper::DEFAULT_TOKEN;
    }

    /**
     * Mark api as logged
     */
    public function setLoggedIn()
    {
        $this->csrfToken = CsrfHelper::getTokenFromFile($this->cookieJar);
        if ( ! empty($this->csrfToken)) {
            $this->loggedIn = true;
        }
    }

    /**
     * Get log status
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->loggedIn;
    }

    /**
     * @param array|object $data
     * @param string|null  $sourceUrl
     * @param array        $bookmarks
     * @return array
     */
    public static function createRequestData($data = [], $sourceUrl = '/', $bookmarks = [])
    {
        if (empty($data)) {
            $data = self::createEmptyRequestData();
        }

        if ( ! empty($bookmarks)) {
            $data["options"]["bookmarks"] = $bookmarks;
        }

        $data["context"] = new \stdClass();

        return [
            "source_url" => $sourceUrl,
            "data"       => json_encode($data),
        ];
    }

    /**
     * @return array
     */
    protected static function createEmptyRequestData()
    {
        return array('options' => []);
    }

    /**
     * @return array
     */
    protected function getDefaultHttpOptions()
    {
        $options = [
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

        return $options;
    }

    /**
     * @return array
     */
    protected function getDefaultHttpHeaders()
    {
        return array_merge($this->requestHeaders, ['X-CSRFToken: '.$this->csrfToken]);
    }

    /**
     * @param array $options
     * @return mixed
     */
    protected function addDefaultCsrfInfo($options)
    {
        $options[CURLOPT_REFERER] = UrlHelper::URL_BASE;
        $options[CURLOPT_HTTPHEADER][] = CsrfHelper::getDefaultCookie();

        return $options;
    }
}
