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
     * @param array $data
     * @return array
     */
    protected function createApiResponseWithData($data)
    {
        return $this->createApiResponse(['data' => $data]);
    }

    /**
     * Create a not found dummy response.
     *
     * @return array
     */
    protected function createNotFoundApiResponse()
    {
        return [
            'api_error_code' => 404,
            'message'        => 'Not found',
        ];
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
}
