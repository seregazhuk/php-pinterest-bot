<?php

namespace seregazhuk\tests\Helpers;

use seregazhuk\PinterestBot\Api\Request;

/**
 * Class FollowResponseHelper
 * @package seregazhuk\tests\Helpers
 *
 * @property Request|MockeryInterface $request
 */
trait FollowResponseHelper
{
    /**
     * @param int $entityId
     * @param string $followUrl
     * @return $this
     */
    protected function apiShouldNotFollow($entityId, $followUrl)
    {
        $this->setFollowRequest(
            $entityId, $followUrl, $this->createErrorApiResponse()
        );

        return $this;
    }

    /**
     * @param int $entityId
     * @param string $followUrl
     * @return $this
     */
    protected function apiShouldFollowTo($entityId, $followUrl)
    {
        $this->setFollowRequest(
            $entityId, $followUrl, $this->createSuccessApiResponse()
        );

        return $this;
    }

    /**
     * @param int $entityId
     * @param string $followUrl
     * @param array $response
     * @return mixed
     */
    protected function setFollowRequest($entityId, $followUrl, $response)
    {
        $this->request
            ->shouldReceive('exec')
            ->once()
            ->withArgs([
                $followUrl,
                $this->provider->createFollowRequestQuery($entityId)
            ])
            ->andReturn(json_encode($response));

        return $this;
    }
}
