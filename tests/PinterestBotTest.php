<?php
use szhuk\PinterestAPI\PinterestBot;
use szhuk\PinterestAPI\ApiRequest;

class PinterestBotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PinterestBot;
     */
    protected $bot;

    /**
     * @var Mockable
     */
    protected $mock;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    protected function setUp()
    {
        $this->bot        = new PinterestBot('test', 'test', new ApiRequest());
        $this->reflection = new \ReflectionClass($this->bot);
    }

    protected function tearDown()
    {
        $this->bot        = null;
        $this->reflection = null;
    }

    public function getProperty($property)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($this->bot);
    }

    /**
     * Call protected methods of PinterestBot class
     *
     * @param string $name
     * @param array  $args
     * @return mixed
     */
    public function invokeMethod($name, $args)
    {
        $method = $this->reflection->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($this->bot, $args);
    }

    public function setProperty($property, $value)
    {
        $property = $this->reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($this->bot, $value);
    }


    public function testLogin()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(0))->method('exec')->willReturn([]);
        $mock->expects($this->at(1))->method('exec')->willReturn(null);
        $this->setProperty('api', $mock);
        $this->assertTrue($this->bot->login());
        $this->assertFalse($this->bot->login());
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must set username and password to login.
     */
    public function testLoginWithoutUsernameOrPassword()
    {
        $this->setProperty('username', null);
        $this->setProperty('password', null);
        $this->bot->login();
    }

    /**
     * @expectedException LogicException
     * @expectedExceptionMessage You must log in before.
     */
    public function testCheckIsLoggedThrowsException()
    {
        $this->bot->checkLoggedIn();
    }


    public function testCheckErrorInResponse()
    {
        $response = [
            [
                'api_error_code' => 404,
                'message'        => 'Not found',
            ],
        ];
        $this->invokeMethod('checkErrorInResponse', $response);
        $this->assertEquals($response[0]['api_error_code'], $this->bot->lastApiErrorCode);
        $this->assertEquals($response[0]['message'], $this->bot->lastApiErrorMsg);

        $this->invokeMethod('checkErrorInResponse', [[]]);
        $this->assertNull($this->bot->lastApiErrorCode);
        $this->assertNull($this->bot->lastApiErrorMsg);
    }
}
