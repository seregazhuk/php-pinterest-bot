<?php

namespace seregazhuk\tests\Bot;

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
        $response->fill($this->createApiResponse(['data' => 'some data']));

        $this->assertEquals('some data', $response->getResponseData());
    }

    /** @test */
    public function it_should_return_value_by_key_from_response()
    {
        $response = new Response();
        $response->fill($this->createApiResponse(['data' => ['key' => 'value']]));

        $this->assertEquals('value', $response->getResponseData('key'));
    }

    /** @test */
    public function it_should_return_false_on_error_response()
    {
        $response = new Response();
        $response->fill($this->createErrorApiResponse('some error'));

        $this->assertFalse($response->getResponseData());

        $lastError = $response->getLastError();
        $this->assertEquals('some error', $lastError['message']);
    }

    /** @test */
    public function it_should_check_empty_responses()
    {
        $response = new Response();

        $this->assertTrue($response->isEmpty());

        $responseWithError = new Response();
        $response->fill($this->createErrorApiResponse('some error'));

        $this->assertTrue($responseWithError->isEmpty());
    }

    /** @test */
    public function it_should_check_responses_with_data()
    {
        $response = new Response();
        $response->fill($this->createSuccessApiResponse());

        $this->assertFalse($response->isEmpty());
    }

    /** @test */
    public function it_should_check_responses_with_errors()
    {
        $response = new Response();
        $response->fill($this->createErrorApiResponse('some error'));
        $this->assertTrue($response->hasErrors());

        $response = new Response();
        $response->fill($this->createSuccessApiResponse());
        $this->assertFalse($response->hasErrors());
    }


    /** @test */
    public function it_should_return_bookmarks_string_from_response()
    {
        $response = new Response();
        $response->fill(['resource' => ['options' => ['bookmarks' => ['my_bookmarks_string']]]]);
        $this->assertEquals(['my_bookmarks_string'], $response->getBookmarks());

        $response = new Response();
        $this->assertEmpty($response->getBookmarks());
    }

    /** @test */
    public function it_should_return_empty_array_for_response_without_pagination()
    {
        $response = new Response();
        $this->assertEmpty($response->getPaginationData());

        $response = new Response();
        $response->fill($this->createApiResponse());
        $this->assertEmpty($response->getPaginationData());
    }

    /** @test */
    public function it_should_return_client_info()
    {
        $response = new Response();
        $clientInfo = ['ip' => '127.0.0.1'];
        $response->fill(['client_context' => $clientInfo]);

        $this->assertEquals($clientInfo, $response->getClientInfo());
    }


    /** @test */
    public function it_should_return_data_and_bookmarks_from_response_with_pagination()
    {
        $dataWithBookmarks = [
            'resource'          => [
                'options' => ['bookmarks' => ['my_bookmarks_string']]
            ],
            'resource_response' => [
                'data' => 'some data'
            ]
        ];
        $response = new Response();
        $response->fill($dataWithBookmarks);

        $expected = [
            'data'      => 'some data',
            'bookmarks' => ['my_bookmarks_string']
        ];

        $this->assertEquals($expected, $response->getPaginationData());
    }
}
