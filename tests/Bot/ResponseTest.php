<?php

namespace seregazhuk\tests\Bot;

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;

/**
 * Class ResponseTest.
 */
class ResponseTest extends TestCase
{
    use ResponseHelper;

    /** @test */
    public function it_should_return_data_from_response()
    {
        $response = new Response();
        $response->fill($this->createSuccessApiResponse('some data'));

        $this->assertEquals('some data', $response->getResponseData());
    }

    /** @test */
    public function it_should_return_value_by_key_from_response()
    {
        $response = new Response();
        $response->fill($this->createSuccessApiResponse(['key' => 'value']));

        $this->assertEquals('value', $response->getResponseData('key'));
    }

    /** @test */
    public function it_should_return_false_on_error_response()
    {
        $response = new Response();
        $response->fill($this->createErrorApiResponse('some error'));

        $this->assertFalse($response->getResponseData());
    }

    /** @test */
    public function it_should_store_last_error_message_from_response()
    {
        $response = new Response();
        $response->fill($this->createErrorApiResponse('some error'));

        $this->assertEquals('some error', $response->getLastErrorText());
    }

    /** @test */
    public function it_should_store_last_error_code_from_response()
    {
        $response = new Response();
        $response->fill($this->createErrorApiResponseWithCode('some error'));

        $this->assertEquals('some error', $response->getLastErrorText());
    }

    /** @test */
    public function it_should_check_empty_responses()
    {
        $response = new Response();

        $this->assertTrue($response->isEmpty());

        $responseWithError = new Response();
        $response->fill($this->createErrorApiResponse());

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
        $response->fill($this->createErrorApiResponse());
        $this->assertTrue($response->hasErrors());
    }

    /** @test */
    public function it_should_check_responses_without_errors()
    {
        $response = new Response();
        $response->fill($this->createSuccessApiResponse());
        $this->assertFalse($response->hasErrors());
    }


    /** @test */
    public function it_should_return_bookmarks_string_from_response()
    {
        $response = new Response();
        $response->fill($this->createPaginatedResponse([], 'my_bookmarks_string'));
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
        $response = new Response();
        $response->fill(
            $this->createPaginatedResponse('some data', 'my_bookmarks_string')
        );

        $expected = [
            'data'      => 'some data',
            'bookmarks' => ['my_bookmarks_string']
        ];

        $this->assertEquals($expected, $response->getPaginationData());
    }

    /** @test */
    public function it_can_check_for_containing_certain_keys()
    {
        $response = new Response();
        $response->fill(['key' => 'value']);

        $this->assertTrue($response->hasData('key'));
        $this->assertFalse($response->hasData('foo'));
    }

    /** @test */
    public function it_can_be_filled_with_json_data()
    {
        $response = new Response();
        $data = ['key' => 'value'];
        $response->fillFromJson(json_encode($data));

        $this->assertEquals($data, $response->getData());

        $response->fillFromJson('');
        $this->assertTrue($response->isEmpty());
    }
}
