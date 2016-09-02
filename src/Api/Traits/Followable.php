<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait Followable
 * @package seregazhuk\PinterestBot\Api\Traits
 *
 * @property string $followUrl
 * @property string $unFollowUrl
 */
trait Followable
{
    use HandlesRequest, HasEntityIdName;

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
        return $this
            ->followMethodCall($entityId, $resourceUrl)
            ->isOk();
    }

    /**
     * Executes api call for follow/unfollow user.
     *
     * @param int    $entityId
     * @param string $url
     *
     * @return Response
     */
    public function followMethodCall($entityId, $url)
    {
        return $this
            ->getRequest()
            ->exec($url, $this->createFollowRequestQuery($entityId));
    }

    /**
     * @param integer $entityId
     * @return mixed
     */
    public function createFollowRequestQuery($entityId)
    {
        $entityName = $this->getEntityIdName();

        $dataJson = [
            'options' => [
                $entityName => (string)$entityId,
            ],
            'context' => [],
        ];

        if ($entityName == 'interest_id') {
            $dataJson['options']['interest_list'] = 'favorited';
        }

        $post = ['data' => json_encode($dataJson, JSON_FORCE_OBJECT)];
        return UrlBuilder::buildRequestString($post);
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
