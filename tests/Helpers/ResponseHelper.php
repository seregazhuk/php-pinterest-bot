<?php

namespace seregazhuk\tests\Helpers;

/**
 * Class ResponseHelper.
 *
 * Helper for creating different dummy responses for testing
 */
trait ResponseHelper
{
    /**
     * Create a dummy response from Pinterest.
     *
     * @param array $data
     *
     * @return array
     */
    protected function createApiResponse($data = [])
    {
        return ['resource_response' => $data];
    }

    /**
     * Create a success dummy response.
     *
     * @return array
     */
    protected function createSuccessApiResponse()
    {
        return $this->createApiResponse(['data' => 'success']);
    }

    /**
     * Create an error dummy response.
     *
     * @param string $error
     * @return array
     */
    protected function createErrorApiResponse($error = 'error')
    {
        return $this->createApiResponse(
            [
                'error' => [
                    'message' => $error,
                ],
            ]
        );
    }

    /**
     * Create a dummy paginated response.
     *
     * @return array
     */
    protected function createPaginatedResponse()
    {
        return [
            'resource_response' => [
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
        ];
    }

    /**
     * @param array|null $response
     * @param int $times
     * @param string $method
     * @return $this
     */
    protected function apiShouldReturn($response = [], $times = 1, $method = 'exec')
    {
        $this->request
            ->shouldReceive($method)
            ->times($times)
            ->andReturn(json_encode($response));

        return $this;
    }

    /**
     * @param int $times
     */
    protected function setSuccessResponse($times = 1)
    {
        $this->apiShouldReturn($this->createSuccessApiResponse(), $times);
    }

    /**
     * @param int $times
     */
    protected function setErrorResponse($times = 1)
    {
        $this->apiShouldReturn($this->createErrorApiResponse(), $times);
    }

    /**
     * @param int $times
     * @return $this
     */
    protected function apiShouldReturnEmpty($times = 1)
    {
        return $this->apiShouldReturnData([], $times);
    }

    /**
     * @param mixed $data
     * @param int $times
     * @return $this
     */
    protected function apiShouldReturnData($data, $times = 1)
    {
        return $this->apiShouldReturn(['resource_response' => ['data' => $data]], $times);
    }

    /**
     * @return $this
     */
    protected function apiShouldReturnSuccess()
    {
        return $this->apiShouldReturn(
            $this->createSuccessApiResponse()
        );
    }

    /**
     * @return $this
     */
    protected function apiShouldReturnError()
    {
        return $this->apiShouldReturn(
            $this->createErrorApiResponse()
        );
    }

    /**
     * @return $this
     */
    protected function apiShouldReturnPagination()
    {
        return $this->apiShouldReturn(
            $this->createPaginatedResponse()
        );
    }
}
