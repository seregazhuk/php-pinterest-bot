<?php

namespace szhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Response;

/**
 * Class ResponseTest.
 */
class ResponseTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function getDataReturnsAllData()
    {
        $response = new Response();

        $data = ['resource_response' => ['data' => 'some data']];
        $this->assertEquals('some data', $response->getData($data));
    }

    /** @test */
    public function getDataReturnsValueByKey()
    {
        $response = new Response();

        $data = [
            'resource_response' => [
                'data' => ['key' => 'value']
            ]
        ];
        $this->assertEquals('value', $response->getData($data, 'key'));
    }

    /** @test */
    public function getDataReturnsFalse()
    {
        $response = new Response();

        $data = ['resource_response' => ['error' => 'some error']];
        $this->assertFalse($response->getData($data));
        $this->assertEquals('some error', $response->getLastError());
    }

    /** @test */
    public function isEmptyReturnsTrueOnEmptyDataOrErrors()
    {
        $response = new Response();

        $this->assertTrue($response->isEmpty([]));

        $dataWithErrors = ['resource_response' => ['error' => 'some error']];
        $this->assertTrue($response->isEmpty($dataWithErrors));
    }

    /** @test */
    public function isEmptyReturnsFalseForData()
    {
        $response = new Response();

        $data = ['resource_response' => ['data' => 'some data']];

        $this->assertFalse($response->isEmpty($data));
    }

    /** @test */
    public function hasErrorsReturnsTrueForDataWithErrorField()
    {
        $response = new Response();

        $dataWithErrors = ['resource_response' => ['error' => 'some error']];
        $this->assertTrue($response->hasErrors($dataWithErrors));
        $this->assertEquals('some error', $response->getLastError());
    }

    /** @test */
    public function hasErrorsReturnsFalseForData()
    {
        $response = new Response();

        $data = ['resource_response' => ['data' => 'some data']];

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
        $data = ['resource_response' => ['error' => 'some error']];

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
