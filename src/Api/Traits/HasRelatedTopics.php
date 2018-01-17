<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait HasRelatedTopics
{
    use HandlesRequest;

    /**
     * Returns a list of related topics.
     *
     * @param string $interest
     * @return array|bool
     */
    public function getRelatedTopics($interest)
    {
        return $this->get(
            UrlBuilder::RESOURCE_GET_CATEGORIES_RELATED,
            ['interest_name' => $interest]
        );
    }
}
