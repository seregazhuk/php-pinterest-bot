<?php

namespace szhuk\tests;

use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Http;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Helpers\CsrfHelper;
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
        $this->request = new Request(new Http());
        $this->reflection = new \ReflectionClass($this->request);
        $this->setReflectedObject($this->request);
    }

    protected function tearDown()
    {
        $this->request = null;
        $this->mock = null;
        $this->reflection = null;
    }

    /** @test */
    public function logInSuccess()
    {
        $this->request->setLoggedIn();
        $this->assertTrue($this->request->checkLoggedIn());
        $token = $this->getProperty('csrfToken');
        $this->assertNotEquals(CsrfHelper::DEFAULT_TOKEN, $token);
        $this->assertTrue($this->request->isLoggedIn());

        $this->request->clearToken();
        $token = $this->getProperty('csrfToken');
        $this->assertEquals(CsrfHelper::DEFAULT_TOKEN, $token);


    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function logInFails()
    {
        $this->setProperty('loggedIn', false);
        $this->request->checkLoggedIn();

        $this->request->setLoggedIn();
        $this->assertTrue($this->request->checkLoggedIn());
        $token = $this->getProperty('csrfToken');
        $this->assertNotEquals(CsrfHelper::DEFAULT_TOKEN, $token);
        $this->assertTrue($this->request->isLoggedIn());

        $this->request->clearToken();
        $token = $this->getProperty('csrfToken');
        $this->assertEquals(CsrfHelper::DEFAULT_TOKEN, $token);

        $this->setProperty('loggedIn', false);
        $this->request->checkLoggedIn();
    }

    /** @test */
    public function executeRequestToPinterestApi()
    {
        $httpMock = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
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
        $mock = $this->getMock(Http::class, ['setOptions', 'execute', 'close']);
        $mock->expects($this->at(1))->method('execute')->willReturn(json_encode($response));
        $mock->expects($this->at(2))->method('execute')->willReturn(null);

        $this->setProperty('http', $mock);
        $this->assertEquals($response, $this->request->followMethodCall(1, Request::BOARD_ENTITY_ID, 'ur'));
        $this->assertNull($this->request->followMethodCall(1, Request::INTEREST_ENTITY_ID, 'ur'));
    }

}
