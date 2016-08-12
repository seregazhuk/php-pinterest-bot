<?php

namespace seregazhuk\PinterestBot\Api;

class Response
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array|null
     */
    protected $lastError;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Check if specified data exists in response.
     *
     * @param null  $key
     *
     * @return array|bool
     */
    public function getData($key = null)
    {
        if ($this->hasErrors()) {
            return false;
        }

        return $this->parseData($key);
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->data;
    }

    /**
     * Parse data from Pinterest Api response.
     * Data is stored in ['resource_response']['data'] array.
     *
     * @param string $key
     *
     * @return bool|array
     */
    protected function parseData($key)
    {
        if (isset($this->data['resource_response']['data'])) {
            $data = $this->data['resource_response']['data'];

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
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getData());
    }

    public function isOk()
    {
        return !$this->hasErrors();
    }

    /**
     * @return bool
     */
    public function hasData()
    {
        return isset($this->data['data']) && !empty($this->data['data']);
    }

    /**
     * Remove 'module' data from response.
     *
     * @return array mixed
     */
    public function clearResponseFromMetaData()
    {
        if (isset($this->data['data'][0]['type']) && $this->data['data'][0]['type'] == 'module') {
            array_shift($this->data['data']);
        }

        return $this->data;
    }

    /**
     * Check for error info in api response and save
     * it.
     *
     * @return bool
     */
    public function hasErrors()
    {
        $this->lastError = null;

        if (isset($this->data['resource_response']['error']) && !empty($this->data['resource_response']['error'])) {
            $this->lastError = $this->data['resource_response']['error'];

            return true;
        }

        return false;
    }

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks()
    {
        if (!$this->hasErrors() && isset($this->data['resource']['options']['bookmarks'][0])) {
            return [$this->data['resource']['options']['bookmarks'][0]];
        }

        return [];
    }

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @return array
     */
    public function getPaginationData()
    {
        if ($this->isEmpty() && $this->hasErrors()) {
            return [];
        }

        $bookmarks = $this->getBookmarks();
        if ($data = $this->getData()) {
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

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->data;
    }

}
