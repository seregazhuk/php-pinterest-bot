<?php

namespace seregazhuk\PinterestBot\Api;

class SearchResponse
{
    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function hasResponseData()
    {
        $searchResults = $this->response->getData('module.tree.data.results', []);

        return $searchResults ? : $this->response->hasResponseData();
    }

    /**
     * Parse bookmarks from response.
     *
     * @return array
     */
    public function getBookmarks()
    {
        $searchBookmarks = $this->response->getData('module.tree.resource.options.bookmarks', []);

        return $searchBookmarks ? [$searchBookmarks[0]] : $this->response->getBookmarks();
    }

    /**
     * @return array
     */
    public function getResponseData()
    {
        $results = $this->response->getData('module.tree.data.results', []);

        return $results ? : $this->response->getResponseData();
    }
}