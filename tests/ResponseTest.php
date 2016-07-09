<?php

namespace szhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;

/**
 * Class ResponseTest.
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    use ResponseHelper;

    /** @test */
    public function it_should_return_data_from_response()
    {
        $response = new Response();

        $data = $this->createApiResponse(['data' => 'some data']);

        $this->assertEquals('some data', $response->getData($data));
    }

    /** @test */
    public function it_should_return_value_by_key_from_response()
    {
        $response = new Response();

        $data = $this->createApiResponse(['data' => ['key' => 'value']]);

        $this->assertEquals('value', $response->getData($data, 'key'));
    }

    /** @test */
    public function it_should_return_false_on_error_response()
    {
        $response = new Response();

        $data = $this->createErrorApiResponse('some error');
        $this->assertFalse($response->getData($data));

        $lastError = $response->getLastError();
        $this->assertEquals('some error', $lastError['message']);
    }

    /** @test */
    public function it_should_check_empty_responses()
    {
        $response = new Response();

        $this->assertTrue($response->isEmpty([]));

        $dataWithErrors = $this->createErrorApiResponse('some error');

        $this->assertTrue($response->isEmpty($dataWithErrors));
    }

    /** @test */
    public function it_should_check_responses_with_data()
    {
        $response = new Response();

        $data = $this->createSuccessApiResponse();

        $this->assertFalse($response->isEmpty($data));
    }

    /** @test */
    public function it_should_check_responses_with_errors()
    {
        $response = new Response();

        $dataWithErrors = $this->createErrorApiResponse('some error');
        $this->assertTrue($response->hasErrors($dataWithErrors));

        $data = $this->createSuccessApiResponse();
        $this->assertFalse($response->hasErrors($data));
    }


    /** @test */
    public function it_should_return_bookmarks_string_from_response()
    {
        $response = new Response();

        $dataWithBookmarks = ['resource' => ['options' => ['bookmarks' => ['my_bookmarks_string']]]];
        $this->assertEquals(['my_bookmarks_string'], $response->getBookmarks($dataWithBookmarks));

        $this->assertEmpty($response->getBookmarks([]));
    }

    /** @test */
    public function it_should_return_empty_array_for_response_without_pagination()
    {
        $response = new Response();
        $this->assertEmpty($response->getPaginationData([]));

        $data = $this->createApiResponse();
        $this->assertEmpty($response->getPaginationData($data));
    }


    /** @test */
    public function it_should_return_data_and_bookmarks_from_response_with_pagination()
    {
        $response = new Response();
        $dataWithBookmarks = [
            'resource'          => [
                'options' => ['bookmarks' => ['my_bookmarks_string']]
            ],
            'resource_response' => [
                'data' => 'some data'
            ]
        ];

        $expected = [
            'data'      => 'some data',
            'bookmarks' => ['my_bookmarks_string']
        ];

        $this->assertEquals($expected, $response->getPaginationData($dataWithBookmarks));
    }
}
