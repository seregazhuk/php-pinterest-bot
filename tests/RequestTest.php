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
        Mockery::close();
        $this->request = null;
        $this->mock = null;
        $this->reflection = null;
    }

    /** @test */
    public function checkLoggedInFailure()
    {
        $this->setProperty('loggedIn', false);
        $this->assertFalse($this->request->isLoggedIn());
    }

    /** @test */
    public function checkLoggedInSuccess()
    {
        $this->setProperty('loggedIn', true);
        $this->assertTrue($this->request->isLoggedIn());
    }

    /** @test */
    public function executeRequestToPinterestApi()
    {
        $httpMock = $this->getHttpMock();

        $response = $this->createSuccessApiResponse();
        $httpMock->shouldReceive('execute')->andReturn(json_encode($response));
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
        $mock = $this->getHttpMock();
        $mock->shouldReceive('execute')->once()->andReturn(json_encode($response));
        $mock->shouldReceive('execute')->once()->andReturnNull();

        $this->setProperty('http', $mock);
        $this->assertEquals($response, $this->request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertNull($this->request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));
    }

    /**
     * @return Mockery\Mock
     */
    protected function getHttpMock()
    {
        $mock = Mockery::mock(HttpInterface::class)->shouldDeferMissing();
        $mock->shouldReceive('init')->andReturnSelf();
        $mock->shouldReceive('setOptions')->andReturnSelf();
        $mock->shouldReceive('close');

        return $mock;
    }
}
