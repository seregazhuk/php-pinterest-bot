<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use Generator;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Trait Followable
 *
 * @property string $followUrl
 * @property string $unFollowUrl
 * @property string $followersUrl
 * @property string $followersFor
 * @property Response $response
 * @property Request $request
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
        $result = $this->request
            ->exec($resourceUrl, $this->createFollowRequestQuery($entityId));

        $this->processResult($result);

        return $this->response->isOk();
    }

    /**
     * @param integer $entityId
     * @return string
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
     * Get followers.
     *
     * @param string $for
     * @param int $limit
     *
     * @return Generator
     */
    public function followers($for, $limit = 0)
    {
        return $this->getFollowData(
            [$this->getFollowersFor() => $for], $this->getFollowersUrl(), $limit
        );
    }

    /**
     * @param array  $data
     * @param string $resourceUrl
     * @param int $limit
     *
     * @return Generator
     */
    public function getFollowData($data, $resourceUrl, $limit = 0)
    {
        $requestData = array_merge([$data, $resourceUrl]);

        return $this->getPaginatedResponse($requestData, $limit);
    }

    /**
     * @param array $params
     * @param int $limit
     * @param string $method
     * @return Generator
     */
    abstract protected function getPaginatedResponse(array $params, $limit, $method = 'getPaginatedData');

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

    /**
     * @param string $res
     * @return Response
     */
    abstract protected function processResult($res);
}
