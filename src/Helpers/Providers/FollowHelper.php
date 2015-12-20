<?php

namespace seregazhuk\PinterestBot\Helpers\Providers;

trait FollowHelper
{
    use ProviderHelper;

    /**
     * Follow user by user_id
     *
     * @param $entityId
     * @return bool
     */
    public function follow($entityId)
    {
        return $this->followCall($entityId, $this->getFollowUrl());
    }

    /**
     * UnFollow user by user_id
     *
     * @param $entityId
     * @return bool
     */
    public function unFollow($entityId)
    {
        return $this->followCall($entityId, $this->getUnfFollowUrl());
    }

    /**
     * Make api call for follow/unfollow a entity (user/board)
     * @param int    $entityId
     * @param string $resourceUrl
     * @return bool
     */
    protected function followCall($entityId, $resourceUrl)
    {
        $this->getRequest()->checkLoggedIn();
        $response = $this->getRequest()->followMethodCall($entityId, $this->getEntityIdName(), $resourceUrl);

        return $this->getResponse()->checkErrorInResponse($response);
    }

    abstract function getEntityIdName();

    abstract function getFollowUrl();

    abstract function getUnfFollowUrl();
}