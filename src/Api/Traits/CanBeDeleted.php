<?php

namespace seregazhuk\PinterestBot\Api\Traits;

/**
 * Trait CanBeDeleted
 *
 * @property string $deleteUrl
 */
trait CanBeDeleted
{
    use HandlesRequest, HasEntityIdName;

    protected function requiresLoginForCanBeDelete()
    {
        return [
            'delete',
        ];
    }

    /**
     * Delete entity by ID.
     *
     * @param int $entityId
     *
     * @return bool
     */
    public function delete($entityId)
    {
        return $this->post(
            $this->getDeleteUrl(),
            [$this->getEntityIdName() => $entityId]
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
