<?php

namespace seregazhuk\PinterestBot\Api\Providers\Core;

/**
 * Class EntityProvider
 * @package seregazhuk\PinterestBot\Api\Providers
 *
 * @property string entityIdName
 */
abstract class EntityProvider extends Provider
{
    /**
     * @var string
     */
    protected $entityIdName;

    /**
     * @return string
     */
    protected function getEntityIdName()
    {
        return property_exists($this, 'entityIdName') ? $this->entityIdName : '';
    }
}
