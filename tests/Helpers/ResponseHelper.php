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
    public function createApiResponse($data = [])
    {
        return ['resource_response' => $data];
    }

    /**
     * Create a success dummy response.
     *
     * @return array
     */
    public function createSuccessApiResponse()
    {
        return $this->createApiResponse(['data' => 'success']);
    }

    /**
     * Create an error dummy response.
     *
     * @param string $error
     * @return array
     */
    public function createErrorApiResponse($error = 'error')
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
    public function createPaginatedResponse()
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
    public function apiShouldReturn($response = [], $times = 1, $method = 'exec')
    {
        $this->request
            ->shouldReceive($method)
            ->times($times)
            ->andReturn(json_encode($response));

        return $this;
    }

    /**
     * @param int $times
     * @return $this
     */
    public function apiShouldReturnEmpty($times = 1)
    {
        return $this->apiShouldReturnData([], $times);
    }

    /**
     * @param mixed $data
     * @param int $times
     * @return $this
     */
    public function apiShouldReturnData($data, $times = 1)
    {
        return $this->apiShouldReturn(['resource_response' => ['data' => $data]], $times);
    }

    /**
     * @param int $times
     * @return $this
     */
    public function apiShouldReturnSuccess($times = 1)
    {
        return $this->apiShouldReturn(
            $this->createSuccessApiResponse(),
            $times
        );
    }

    /**
     * @param int $times
     * @return $this
     */
    public function apiShouldReturnError($times = 1)
    {
        return $this->apiShouldReturn(
            $this->createErrorApiResponse(),
            $times
        );
    }

    /**
     * @return $this
     */
    public function apiShouldReturnPagination()
    {
        return $this->apiShouldReturn(
            $this->createPaginatedResponse()
        );
    }

    /**
     * @param mixed $response
     * @param int $count
     */
    public function assertIsPaginatedResponse($response, $count = 2)
    {
        $this->assertInstanceOf(\Generator::class, $response);
        $this->assertCount($count, iterator_to_array($response));
    }
}
