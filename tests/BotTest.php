<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Http;
use seregazhuk\PinterestBot\PinterestBot;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Request;

class BotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PinterestBot;
     */
    protected $bot;

    /**
     * @var \Mockable
     */
    protected $mock;

    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    protected function setUp()
    {
        $this->bot = new PinterestBot('test', 'test');
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

    /**
     * @expectedException \LogicException
     */
    public function testLogin()
    {
        $mock = $this->getMock(Request::class, ['exec', 'setLoggedIn'], [new Http()]);
        $mock->expects($this->at(0))->method('exec')->willReturn(true);
        $this->setProperty('request', $mock);
        $this->assertTrue($this->bot->login());

        $this->setProperty('username', null);
        $this->assertTrue($this->bot->login());
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongProvider()
    {
        $this->bot->badProvider;
    }

}
