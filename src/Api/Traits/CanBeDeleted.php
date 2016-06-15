<?php

namespace seregazhuk\PinterestBot\Api\Traits;

/**
 * Class CanBeDeleted
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $deleteUrl
 */
trait CanBeDeleted
{
    use HandlesRequestAndResponse, HasEntityIdName;

    /**
     * Delete entity by ID.
     *
     * @param int $entityId
     *
     * @return bool
     */
    public function delete($entityId)
    {
        return $this->execPostRequest([$this->getEntityIdName() => $entityId], $this->getDeleteUrl());
    }

    protected function getDeleteUrl()
    {
        return property_exists($this, 'deleteUrl') ? $this->deleteUrl : '';
    }
}