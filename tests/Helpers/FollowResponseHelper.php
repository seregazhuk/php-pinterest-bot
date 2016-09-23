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
     * @param string $sourceUrl
     * @return $this
     */
    protected function setFollowErrorResponse($entityId, $sourceUrl)
    {
        $this->setFollowRequest(
            $entityId, $sourceUrl, $this->createErrorApiResponse()
        );

        return $this;
    }

    /**
     * @param int $entityId
     * @param string $sourceUrl
     * @return $this
     */
    protected function setFollowSuccessResponse($entityId, $sourceUrl)
    {
        $this->setFollowRequest(
            $entityId, $sourceUrl, $this->createSuccessApiResponse()
        );

        return $this;
    }

    /**
     * @param int $entityId
     * @param string $sourceUrl
     * @param array $response
     * @return mixed
     */
    protected function setFollowRequest($entityId, $sourceUrl, $response)
    {
        $this->request
            ->shouldReceive('exec')
            ->once()
            ->withArgs([
                $sourceUrl,
                $this->provider->createFollowRequestQuery($entityId)
            ])
            ->andReturn(json_encode($response));

        return $this;
    }
}
