<?php

namespace seregazhuk\PinterestBot\Api\Providers\Core;

use seregazhuk\PinterestBot\Helpers\Pagination;

abstract class FollowableProvider extends EntityProvider
{
    /**
     * @var string
     */
    protected $followUrl;

    /**
     * @var string
     */
    protected $unFollowUrl;

    /**
     * @var string
     */
    protected $followersUrl;

    /**
     * @var string
     */
    protected $followersFor;

    /**
     * @return array
     */
    protected function requiresLoginForFollowableProvider()
    {
        return [
            'follow',
            'unfollow',
        ];
    }

    /**
     * Follow entity by its id.
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
     * UnFollow entity by its id.
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
        $entityId = $this->resolveEntityId($entityId);

        $query = $this->createFollowRequest($entityId);

        return $this->post($query, $resourceUrl);
    }

    /**
     * Is used for *overloading* follow/unfollow methods. When for pinners
     * we can pass either user's name or id.
     *
     * @param mixed $entityId
     * @return int|null
     */
    protected function resolveEntityId($entityId)
    {
        return $entityId;
    }

    /**
     * @param integer $entityId
     * @return array
     */
    protected function createFollowRequest($entityId)
    {
        $entityName = $this->getEntityIdName();

        $dataJson = [$entityName => $entityId];

        if ($entityName == 'interest_id') {
            $dataJson['interest_list'] = 'favorited';
        }

        return $dataJson;
    }

    /**
     * Get followers.
     *
     * @param string $for
     * @param int $limit
     *
     * @return Pagination
     */
    public function followers($for, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            [$this->getFollowersFor() => $for], $this->getFollowersUrl(), $limit
        );
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
        return property_exists($this, 'unFollowUrl') ? $this->unFollowUrl : '';
    }

    /**
     * @return string
     */
    protected function getFollowersUrl()
    {
        return property_exists($this, 'followersUrl') ? $this->followersUrl : '';
    }

    /**
     * @return string
     */
    protected function getFollowersFor()
    {
        return property_exists($this, 'followersFor') ? $this->followersFor : '';
    }
}