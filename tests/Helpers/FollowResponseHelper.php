<?php

namespace seregazhuk\tests\Helpers;

trait FollowResponseHelper 
{
    use SetsResponse;

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
        $this->requestMock->shouldReceive('followMethodCall')->once()->withArgs(
                [
                    $entityId,
                    $this->provider->getEntityIdName(),
                    $sourceUrl
                ]
            )->andReturn($response);

        return $this;
    }
}
