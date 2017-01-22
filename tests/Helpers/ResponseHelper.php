<?php

namespace seregazhuk\tests\Helpers;

use seregazhuk\PinterestBot\Helpers\Pagination;

/**
 * Class ResponseHelper.
 *
 * Helper for creating different dummy responses for testing
 */
trait ResponseHelper
{
    /**
     * @var array
     */
    protected $paginatedResponse = [
        ['id' => 1],
        ['id' => 2],
    ];

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
     * @param $response
     * @return array
     */
    public function createPaginatedResponse($response)
    {
        return [
            'resource_response' => [
                'data' => $response,
            ],
        ];
    }

    /**
     * Create a dummy paginated response.
     *
     * @param $response
     * @return array
     */
    public function createSearchResponse($response)
    {
        return [
           'module' => [
               'tree' => [
                   'data' => [
                       'results' => $response
                   ]
               ]
            ]
        ];
    }


    /**
     * @param array|null $response
     * @param int $times
     * @return $this
     */
    public function apiShouldReturn($response = [], $times = 1)
    {
        $this->request
            ->shouldReceive('exec')
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
     * @param $response
     * @return $this
     */
    public function apiShouldReturnPagination($response)
    {
        return $this->apiShouldReturn(
            $this->createPaginatedResponse($response)
        );
    }

    /**
     * @param array $response
     * @return $this
     */
    public function apiShouldReturnSearchPagination($response)
    {
        return $this->apiShouldReturn(
            $this->createSearchResponse($response)
        );
    }

    /**
     * @param mixed $response
     * @return $this
     */
    public function assertIsPaginatedResponse($response)
    {
        $this->assertInstanceOf(\Traversable::class, $response);

        return $this;
    }

    /**
     * @param $expected
     * @param Pagination $response
     */
    public function assertPaginatedResponseEquals($expected, Pagination $response)
    {
        $this->assertEquals($expected, $response->toArray());
    }
}
