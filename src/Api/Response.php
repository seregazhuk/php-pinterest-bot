<?php

namespace seregazhuk\PinterestBot\Api;

class Response
{
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
        if ($this->hasErrors($response)) {
            return false;
        }

        return $this->parseData($response, $key);
    }

    /**
     * Parse data from Pinterest Api response.
     * Data is stored in ['resource_response']['data'] array.
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
     * Checks if response is empty.
     *
     * @param array $response
     *
     * @return bool
     */
    public function isEmpty($response)
    {
        return empty($this->getData($response));
    }

    /**
     * Check for error info in api response and save
     * it.
     *
     * @param array $response
     *
     * @return bool
     */
    public function hasErrors($response)
    {
        $this->lastError = null;

        if (isset($response['resource_response']['error']) && !empty($response['resource_response']['error'])) {
            $this->lastError = $response['resource_response']['error'];

            return true;
        }

        return false;
    }

    /**
     * Parse bookmarks from response.
     *
     * @param array $response
     *
     * @return array
     */
    public function getBookmarks($response)
    {
        if (!$this->hasErrors($response) && isset($response['resource']['options']['bookmarks'][0])) {
            return [$response['resource']['options']['bookmarks'][0]];
        }

        return [];
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
        if ($this->isEmpty($response) && $this->hasErrors($response)) {
            return [];
        }

        $bookmarks = $this->getBookmarks($response);
        if ($data = $this->getData($response)) {
            return ['data' => $data, 'bookmarks' => $bookmarks];
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
