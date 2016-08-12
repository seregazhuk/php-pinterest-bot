<?php

namespace seregazhuk\PinterestBot\Api\Traits;

/**
 * Trait CanBeDeleted
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $deleteUrl
 */
trait CanBeDeleted
{
    use HandlesRequest, HasEntityIdName;

    /**
     * Delete entity by ID.
     *
     * @param int $entityId
     *
     * @return bool
     */
    public function delete($entityId)
    {
        return $this->execPostRequest(
            [$this->getEntityIdName() => $entityId], $this->getDeleteUrl()
        );
    }

    /**
     * @return string
     */
    protected function getDeleteUrl()
    {
        return property_exists($this, 'deleteUrl') ? $this->deleteUrl : '';
    }
}