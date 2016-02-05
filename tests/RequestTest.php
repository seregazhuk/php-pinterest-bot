<?php

namespace szhuk\tests;

use LogicException;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\tests\helpers\ResponseHelper;
use seregazhuk\tests\helpers\ReflectionHelper;

class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    /**
     * @var Request;
     */
    protected $request;

    /**
     * Mock
     */
    public $mock;

    protected function setUp()
    {
        $this->request = new Request(new CurlAdapter());
        $this->reflection = new ReflectionClass($this->request);
        $this->setReflectedObject($this->request);
    }

    protected function tearDown()
    {
        $this->request = null;
        $this->mock = null;
        $this->reflection = null;
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function checkLoggedInFailure()
    {
        $this->assertFalse($this->request->checkLoggedIn());
    }

    /** @test */
    public function checkLoggedInSuccess()
    {
        $this->setProperty('loggedIn', true);
        $this->assertTrue($this->request->checkLoggedIn());
    }

    /** @test */
    public function executeRequestToPinterestApi()
    {
        $httpMock = $this->getMock(CurlAdapter::class, ['setOptions', 'execute', 'close']);
        $response = $this->createSuccessApiResponse();
        $httpMock->method('execute')->willReturn(json_encode($response));
        $this->setProperty('http', $httpMock);
        $res = $this->request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);

        $this->request->clearToken();
        $res = $this->request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);
    }

    /** @test */
    public function executeFollowRequestToPinterestApi()
    {
        $response = $this->createSuccessApiResponse();
        $mock = $this->getMock(CurlAdapter::class, ['setOptions', 'execute', 'close']);
        $mock->expects($this->at(1))->method('execute')->willReturn(json_encode($response));
        $mock->expects($this->at(2))->method('execute')->willReturn(null);

        $this->setProperty('http', $mock);
        $this->assertEquals($response, $this->request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertNull($this->request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));
    }
}
