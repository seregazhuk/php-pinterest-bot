<?php

namespace seregazhuk\PinterestBot\Api;

class SearchResponse extends Response
{
    protected function getRawBookmarksData()
    {
        return $this->getData('module.tree.resource.options.bookmarks', []);
    }

    /**
     * @param null $key
     * @return array
     */
    public function getResponseData($key = null)
    {
        return $this->getData('module.tree.data.results', []);
    }
}