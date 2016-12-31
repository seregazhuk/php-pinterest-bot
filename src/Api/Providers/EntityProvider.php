<?php

namespace seregazhuk\PinterestBot\Api\Providers;

class EntityProvider extends Provider
{
    /**
     * @return string
     */
    public function getEntityIdName()
    {
        return property_exists($this, 'entityIdName') ? $this->entityIdName : '';
    }
}