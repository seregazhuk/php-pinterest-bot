<?php

namespace seregazhuk\PinterestBot\Api;

use seregazhuk\PinterestBot\Api\Contracts\PaginatedResponse;
use function seregazhuk\PinterestBot\get_array_data;

class Response implements PaginatedResponse
{
    /**
     * @var mixed
     */
    protected $data = [];

    /**
     * @var Error
     */
    protected $lastError;

    /**
     * @var array|null
     */
    protected $clientInfo;

    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->lastError = new Error();

        if ($data) {
            $this->fill($data);
        }
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function fill($data)
    {
        $this->data = $data;

        return $this
            ->fillError()
            ->fillClientInfo();
    }

    /**
     * @param string $json
     * @return static
     */
    public function fillFromJson($json)
    {
        return $this->fill(json_decode($json, true));
    }

    /**
     * Check if specified data exists in response.
     *
     * @param null $key
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
     * @param null|mixed $default
     * @return mixed
     */
    public function getData($key = '', $default = null)
    {
        return get_array_data($key, $this->data, $default);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')
    {
        return get_array_data($key, $this->data) !== null;
    }

    /**
     * Parse data from Pinterest Api response.
     * Data is stored in ['resource_response']['data'] array.
     *
     * @param string|null $key
     * @return bool|array
     */
    protected function parseResponseData($key = null)
    {
        $responseData = get_array_data('resource_response.data', $this->data);

        if (!$responseData) {
            return false;
        }

        return $key ?
            get_array_data($key, $responseData) :
            $responseData;
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
        return !$this->hasErrors();
    }

    /**
     * Check for error info in api response and save it.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->lastError->isEmpty();
    }

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks()
    {
        $bookmarks = $this->getRawBookmarksData();

        if (empty($bookmarks)) {
            return [];
        }

        if ($bookmarks[0] === '-end-') {
            return [];
        }

        return $bookmarks;
    }

    /**
     * @return bool
     */
    public function hasBookmarks()
    {
        return (bool) $this->getBookmarks();
    }

    protected function getRawBookmarksData()
    {
        return $this->getData('resource.options.bookmarks', []);
    }

    /**
     * Checks Pinterest API paginated response, and parses data
     * with bookmarks info from it.
     *
     * @return array
     */
    public function getPaginationData()
    {
        if ($this->hasErrors()) {
            return [];
        }

        if ($data = $this->getResponseData()) {
            return [
                'data'      => $data,
                'bookmarks' => $this->getBookmarks(),
            ];
        }

        return [];
    }

    /**
     * @return string|null
     */
    public function getLastErrorText()
    {
        return $this->lastError->getText();
    }

    /**
     * @return array|null
     */
    public function getClientInfo()
    {
        return $this->clientInfo;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->data;
    }

    public function clear()
    {
        return $this->fill([]);
    }

    protected function fillError()
    {
        $errorData = get_array_data('resource_response.error', $this->data);

        $this->lastError = new Error($errorData);

        return $this;
    }

    protected function fillClientInfo()
    {
        $this->clientInfo = get_array_data('client_context', $this->data);

        return $this;
    }
}
