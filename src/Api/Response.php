<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;

class Response implements PaginatedResponse
{
    /**
     * @var mixed
     */
    protected $data = [];

    /**
     * @var array|null
     */
    protected $lastError;

    /**
     * @var array|null
     */
    protected $clientInfo;

    public function fill($data)
    {
        $this->data = $data;

        $this->lastError = $this->getValueByKey('resource_response.error', $this->data);

        $this->clientInfo = $this->getValueByKey('client_context', $this->data);
    }

    /**
     * Check if specified data exists in response.
     *
     * @param null  $key
     *
     * @return array|bool
     */
    public function getResponseData($key = null)
    {
        if ($this->hasErrors()) {
            return false;
        }

        return $this->parseResponseData($key);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function getData($key = '', $default = null)
    {
        return $this->getValueByKey($key, $this->data, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')
    {
        return !is_null($this->getValueByKey($key, $this->data));
    }

    /**
     * Parse data from Pinterest Api response.
     * Data is stored in ['resource_response']['data'] array.
     *
     * @param string $key
     *
     * @return bool|array
     */
    protected function parseResponseData($key)
    {
        $responseData = $this->getValueByKey('resource_response.data', $this->data);
        if(!$responseData) return false;

        return $key ?
            $this->getValueByKey($key, $responseData) :
            $responseData;
    }

    /**
     * @param string $key
     * @param array $data
     * @param mixed $default
     * @return array|bool|mixed
     */
    protected function getValueByKey($key = '', $data, $default = null)
    {
        if(!is_array($data)) return null;

        if(empty($key)) return $data;

        $indexes = explode('.', $key);
        $value = $data;

        foreach ($indexes as $index) {
            if(!isset($value[$index])) return $default;

            $value = $value[$index];
        }

        return $value;
    }

    /**
     * Checks if response is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getResponseData());
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return !$this->hasErrors() && !$this->isEmpty();
    }

    /**
     * @return bool
     */
    public function hasResponseData()
    {
        return (bool)$this->getValueByKey('resource_response.data', $this->data);
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
        return !is_null($this->lastError);
    }

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks()
    {
        $bookmarks = $this->getValueByKey('resource.options.bookmarks', $this->data,  []);
        return empty($bookmarks) ? [] : [$bookmarks[0]];
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
        if ($data = $this->getResponseData()) {
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
     * @return array|null
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->data;
    }
}
