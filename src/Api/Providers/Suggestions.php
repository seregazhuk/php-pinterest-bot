<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Suggestions extends Provider
{
    /**
     * @param string $query
     * @return array
     */
    public function searchFor($query)
    {
        return (array) $this->get(UrlBuilder::RESOURCE_TYPE_AHEAD_SUGGESTIONS, [
            'term' => $query,
            'pin_scope' => 'pins'
        ]);
    }

    /**
     * @param string $query
     * @return array
     */
    public function tagsFor($query)
    {
        return (array)$this->get(UrlBuilder::RESOURCE_HASHTAG_TYPE_AHEAD_SUGGESTIONS, [
            'query' => '#' . $query,
            'showPinCount' => true
        ]);
    }
}
