<?php

namespace seregazhuk\PinterestBot\Api;

class SearchResponse extends Response
{
    /**
     * @param null $key
     * @return array
     */
    public function getResponseData($key = null)
    {
        // First response is special and returns data in 'resource_response.data.results` array
        $data = $this->getData('resource_response.data.results', []);

        // All the next responses look as expected
        return empty($data) ?
            parent::getResponseData() :
            $data;
    }
}
