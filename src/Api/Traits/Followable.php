<?php

namespace seregazhuk\PinterestBot\Api\Traits;

/**
 * Class Followable
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $followUrl
 * @property string $unFollowUrl
 */
trait Followable
{
    use HandlesRequestAndResponse, HasEntityIdName;

    /**
     * Follow user by user_id.
     *
     * @param $entityId
     *
     * @return bool
     */
    public function follow($entityId)
    {
        return $this->followCall($entityId, $this->getFollowUrl());
    }

    /**
     * UnFollow user by user_id.
     *
     * @param $entityId
     *
     * @return bool
     */
    public function unFollow($entityId)
    {
        return $this->followCall($entityId, $this->getUnFollowUrl());
    }

    /**
     * Make api call for follow/unFollow a entity (user/board).
     *
     * @param int    $entityId
     * @param string $resourceUrl
     *
     * @return bool
     */
    protected function followCall($entityId, $resourceUrl)
    {
        $response = $this->getRequest()->followMethodCall(
                $entityId, $this->getEntityIdName(), $resourceUrl
            );

        return !$this->getResponse()->hasErrors($response);
    }

    /**
     * @return string
     */
    protected function getFollowUrl()
    {
        return property_exists($this, 'followUrl') ? $this->followUrl : '';
    }

    /**
     * @return string
     */
    protected function getUnFollowUrl()
    {
        return property_exists($this, 'followUrl') ? $this->unFollowUrl : '';
    }
}
