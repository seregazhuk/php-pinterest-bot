<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class Suggestions extends Provider
{
    /**
     * @param string $query
     * @return array|bool
     */
    public function forQuery($query)
    {
        return $this->get(UrlBuilder::RESOURCE_TYPE_AHEAD_SUGGESTIONS, [
            'term' => $query,
            'pin_scope' => 'pins'
        ]);
    }
}
