<?php

namespace seregazhuk\PinterestBot\Helpers\Providers\Traits;

trait Followable
{
    use HandlesRequestAndResponse;
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
        return $this->followCall($entityId, $this->getUnfFollowUrl());
    }

    /**
     * Make api call for follow/unfollow a entity (user/board).
     *
     * @param int    $entityId
     * @param string $resourceUrl
     *
     * @return bool
     */
    protected function followCall($entityId, $resourceUrl)
    {
        $response = $this->getRequest()->followMethodCall($entityId, $this->getEntityIdName(), $resourceUrl);

        return $this->getResponse()->hasErrors($response);
    }

    /**
     * @return string
     */
    abstract protected function getEntityIdName();

    /**
     * @return string
     */
    abstract protected function getFollowUrl();

    /**
     * @return string
     */
    abstract protected function getUnfFollowUrl();
}
