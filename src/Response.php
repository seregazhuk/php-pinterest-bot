<?php

namespace seregazhuk\PinterestBot;

use seregazhuk\PinterestBot\Interfaces\ResponseInterface;

class Response implements ResponseInterface
{
    /**
     * @var array
     */
    protected $response;

    /**
     * @var array
     */
    protected $lastError;

    /**
     * Check if specified data exists in response
     * @param      $response
     * @param null $key
     * @return array|bool
     */
    public function getData($response, $key = null)
    {

        if ( ! $this->checkErrorInResponse($response)) {
            return false;
        }

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
     * Checks if response is not empty
     *
     * @param array $response
     * @return bool
     */
    public function notEmpty($response)
    {
        return ! empty($this->getData($response));
    }

    public function checkResponse($response)
    {
        return ($this->notEmpty($response) && $this->checkErrorInResponse($response));
    }

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
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
     * Parse bookmarks from response
     * @param array $response
     * @return string|null
     */
    public function getBookmarksFromResponse($response)
    {
        if ( ! $this->checkErrorInResponse($response)) {
            return null;
        }
        if (isset($response['resource']['options']['bookmarks'][0])) {
            return [$response['resource']['options']['bookmarks'][0]];
        }

        return null;
    }

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @param array $response
     * @return array
     * @internal param array $response
     */
    public function getPaginationData($response)
    {
        if ( ! $this->checkResponse($response)) {
            return [];
        }

        $bookmarks = $this->getBookmarksFromResponse($response);
        if ($data = self::getData($response)) {
            return ['data' => $data, 'bookmarks' => $bookmarks];
        }

        return [];
    }

    /**
     * Parses Pinterest search API response for data and bookmarks
     * for next pagination page
     *
     * @param array $response
     * @param bool  $bookmarksUsed
     * @return array|null
     */
    public function parseSearchResponse($response, $bookmarksUsed)
    {
        if ($response === null || ! $bookmarksUsed) {
            return self::parseSimpledSearchResponse($response);
        }

        return $this->getPaginationData($response);
    }

    /**
     * Parses simple Pinterest search API response
     * on request with bookmarks
     *
     * @param $response
     * @return array
     */
    public function parseSimpledSearchResponse($response)
    {
        $bookmarks = [];

        if (isset($response['module']['tree']['resource']['options']['bookmarks'][0])) {
            $bookmarks = $response['module']['tree']['resource']['options']['bookmarks'][0];
        }

        if ( ! empty($response['module']['tree']['data']['results'])) {
            return ['data' => $response['module']['tree']['data']['results'], 'bookmarks' => [$bookmarks]];
        }

        return [];
    }
}