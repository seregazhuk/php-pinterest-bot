<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Contracts\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @var array|null
     */
    protected $lastError;

    /**
     * Check if specified data exists in response.
     *
     * @param array $response
     * @param null  $key
     *
     * @return array|bool
     */
    public function getData($response, $key = null)
    {
        if (!$this->checkErrorInResponse($response)) {
            return false;
        }

        return $this->parseData($response, $key);
    }

    /**
     * Parse data from Pinterest Api response.
     * Data stores in ['resource_response']['data'] array.
     *
     * @param array  $response
     * @param string $key
     *
     * @return bool|array
     */
    protected function parseData($response, $key)
    {
        if (isset($response['resource_response']['data'])) {
            $data = $response['resource_response']['data'];

            if ($key) {
                return array_key_exists($key, $data) ? $data[$key] : false;
            }

            return $data;
        }

        return false;
    }

    /**
     * Checks if response is not empty.
     *
     * @param array $response
     *
     * @return bool
     */
    public function notEmpty($response)
    {
        return !empty($this->getData($response));
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    public function checkResponse($response)
    {
        return $this->notEmpty($response) && $this->checkErrorInResponse($response);
    }

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
     *
     * @return bool
     */
    public function checkErrorInResponse($response)
    {
        $this->lastError = null;

        if (isset($response['resource_response']['error']) && !empty($response['resource_response']['error'])) {
            $this->lastError = $response['resource_response']['error'];

            return false;
        }

        return true;
    }

    /**
     * Parse bookmarks from response.
     *
     * @param array $response
     *
     * @return array|null
     */
    public function getBookmarksFromResponse($response)
    {
        if ($this->checkErrorInResponse($response) && isset($response['resource']['options']['bookmarks'][0])) {
            return [$response['resource']['options']['bookmarks'][0]];
        }

        return null;
    }


    /**
     * Parses Pinterest search API response for data and bookmarks
     * for next pagination page.
     *
     * @param array $response
     * @param bool  $bookmarksUsed
     *
     * @return array|null
     */
    public function parseSearchResponse($response, $bookmarksUsed = true)
    {
        if ($response === null || !$bookmarksUsed) {
            return self::parseSimpledSearchResponse($response);
        }

        return $this->getPaginationData($response);
    }

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @param array $response
     *
     * @return array
     */
    public function getPaginationData($response)
    {
        if (!$this->checkResponse($response)) {
            return [];
        }

        $bookmarks = $this->getBookmarksFromResponse($response);
        if ($data = self::getData($response)) {
            return ['data' => $data, 'bookmarks' => $bookmarks];
        }

        return [];
    }

    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks.
     *
     * @param array $response
     *
     * @return array
     */
    public function parseSimpledSearchResponse($response)
    {
        $bookmarks = [];
        if (isset($response['module']['tree']['resource']['options']['bookmarks'][0])) {
            $bookmarks = $response['module']['tree']['resource']['options']['bookmarks'][0];
        }

        if (!empty($response['module']['tree']['data']['results'])) {
            return ['data' => $response['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getLastError()
    {
        return $this->lastError;
    }
}
