<?php

namespace szhuk\tests;

use LogicException;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Contracts\HttpInterface;
use seregazhuk\tests\helpers\ResponseHelper;
use seregazhuk\tests\helpers\ReflectionHelper;

/**
 * Class RequestTest
 * @package szhuk\tests
 */
class RequestTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper, ResponseHelper;

    /**
     * @param HttpInterface $http
     * @return Request
     */
    protected function createRequestObject(HttpInterface $http)
    {
        $request = new Request($http);
        $this->reflection = new ReflectionClass($request);
        $this->setReflectedObject($request);

        return $request;
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function checkLoggedInFailure()
    {
        $request = $this->createRequestObject(new CurlAdapter());
        $this->setProperty('loggedIn', false);
        $this->assertFalse($request->isLoggedIn(), 'Failed asserting logged in property');
    }

    /** @test */
    public function checkLoggedInSuccess()
    {
        $request = $this->createRequestObject(new CurlAdapter());
        $this->setProperty('loggedIn', true);
        $this->assertTrue($request->isLoggedIn(), 'Failed asserting logged in property');
    }

    /** @test */
    public function executeRequestToPinterestApi()
    {
        $response = $this->createSuccessApiResponse();
        $http = $this->getHttpMock();
        $http->shouldReceive('execute')->once()->andReturn(json_encode($response));
        $http->shouldReceive('execute')->once()->andReturnNull();

        $request = $this->createRequestObject($http);

        $res = $request->exec('http://example.com', 'a=b');
        $this->assertEquals($response, $res);

        $res = $request->exec('http://example.com', 'a=b');
        $this->assertNull($res);
    }

    /** @test */
    public function executeFollowRequestToPinterestApi()
    {
        $response = $this->createSuccessApiResponse();
        $http = $this->getHttpMock();
        $http->shouldReceive('execute')->once()->andReturn(json_encode($response));
        $http->shouldReceive('execute')->once()->andReturnNull();
        $request = $this->createRequestObject($http);

        $this->assertEquals($response, $request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertNull($request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));
    }

    /**
     * @return Mockery\Mock|HttpInterface
     */
    protected function getHttpMock()
    {
        $mock = Mockery::mock(HttpInterface::class);
        return $mock;
    }
}
