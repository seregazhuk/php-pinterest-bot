<?php

namespace seregazhuk\PinterestBot\Api;

class SearchResponse extends Response
{
    protected function getRawBookmarksData()
    {
        // First response is special and returns bookmarks in 'module.tree` array
        $bookmarks = $this->getData('module.tree.resource.options.bookmarks', []);

        // All the next responses look as expected
        return empty($bookmarks) ?
            parent::getRawBookmarksData() :
            $bookmarks;
    }

    /**
     * @param null $key
     * @return array
     */
    public function getResponseData($key = null)
    {
        // First response is special and returns data in 'module.tree` array
        $data = $this->getData('module.tree.data.results', []);

        // All the next responses look as expected
        return empty($data) ?
            parent::getResponseData() :
            $data;
    }
}