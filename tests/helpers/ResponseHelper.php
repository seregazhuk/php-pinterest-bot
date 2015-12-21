<?php

namespace seregazhuk\tests\helpers;

trait ResponseHelper
{
    /**
     * Creates a response from Pinterest
     * @param array $data
     * @return array
     */
    protected function createApiResponse($data = [])
    {
        return array('resource_response' => $data);
    }

    protected function createSuccessApiResponse()
    {
        return $this->createApiResponse(['data' => 'success']);
    }

    protected function createErrorApiResponse()
    {
        return $this->createApiResponse(['error' => 'error']);
    }

    protected function createNotFoundApiResponse()
    {
        return [
            'api_error_code' => 404,
            'message'        => 'Not found',
        ];
    }

    /**
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