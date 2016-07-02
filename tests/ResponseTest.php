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
    public function getDataReturnsAllData()
    {
        $response = new Response();

        $data = $this->createApiResponse(['data' => 'some data']);

        $this->assertEquals('some data', $response->getData($data));
    }

    /** @test */
    public function getDataReturnsValueByKey()
    {
        $response = new Response();

        $data = $this->createApiResponse(['data' => ['key' => 'value']]);

        $this->assertEquals('value', $response->getData($data, 'key'));
    }

    /** @test */
    public function getDataReturnsFalse()
    {
        $response = new Response();

        $data = $this->createErrorApiResponse('some error');
        $this->assertFalse($response->getData($data));

        $lastError = $response->getLastError();
        $this->assertEquals('some error', $lastError['message']);
    }

    /** @test */
    public function isEmptyReturnsTrueOnEmptyDataOrErrors()
    {
        $response = new Response();

        $this->assertTrue($response->isEmpty([]));

        $dataWithErrors = $this->createErrorApiResponse('some error');

        $this->assertTrue($response->isEmpty($dataWithErrors));
    }

    /** @test */
    public function isEmptyReturnsFalseForData()
    {
        $response = new Response();

        $data = $this->createSuccessApiResponse();

        $this->assertFalse($response->isEmpty($data));
    }

    /** @test */
    public function hasErrorsReturnsTrueForDataWithErrorField()
    {
        $response = new Response();

        $dataWithErrors = $this->createErrorApiResponse('some error');

        $this->assertTrue($response->hasErrors($dataWithErrors));
    }

    /** @test */
    public function hasErrorsReturnsFalseForData()
    {
        $response = new Response();

        $data = $this->createSuccessApiResponse();

        $this->assertFalse($response->hasErrors($data));
    }

    /** @test */
    public function getBookmarksReturnsBookmarksString()
    {
        $response = new Response();

        $dataWithBookmarks = ['resource' => ['options' => ['bookmarks' => ['my_bookmarks_string']]]];
        $this->assertEquals(['my_bookmarks_string'], $response->getBookmarks($dataWithBookmarks));
    }

    /** @test */
    public function getBookmarksReturnsEmptyArrayForNoBookmarks()
    {
        $response = new Response();
        $this->assertEmpty($response->getBookmarks([]));
    }

    /** @test */
    public function getPaginationDataReturnsEmptyArrayForNoPagination()
    {
        $response = new Response();
        $this->assertEmpty($response->getPaginationData([]));
    }

    /** @test */
    public function getPaginationDataReturnsEmptyArrayForErrors()
    {
        $response = new Response();

        $data = $this->createApiResponse();

        $this->assertEmpty($response->getPaginationData($data));
    }

    /** @test */
    public function getPaginationDataReturnDataAndBookmarks()
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
