<?php

namespace seregazhuk\tests;

use Mockable;
use ReflectionClass;
use seregazhuk\PinterestBot\Http;
use seregazhuk\PinterestBot\PinterestBot;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Request;
use seregazhuk\tests\helpers\ReflectionHelper;

class BotTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelper;

    /**
     * @var PinterestBot;
     */
    protected $bot;

    protected function setUp()
    {
        $this->bot = new PinterestBot('test', 'test');
        $this->reflection = new ReflectionClass($this->bot);
    }

    protected function tearDown()
    {
        $this->bot        = null;
        $this->reflection = null;
    }

    /**
     * @expectedException \LogicException
     */
    public function testLogin()
    {
        $mock = $this->getMock(Request::class, ['exec', 'setLoggedIn', 'isLoggedIn'], [new Http()]);
        $mock->method('exec')->willReturn(true);
        $mock->expects($this->at(0))->method('isLoggedIn')->willReturn(true);
        $mock->expects($this->at(1))->method('isLoggedIn')->willReturn(false);

        $this->setProperty($this->bot, 'request', $mock);
        $this->assertTrue($this->bot->login());

        $this->setProperty($this->bot, 'request', $mock);
        $this->assertTrue($this->bot->login());

        $this->setProperty($this->bot, 'username', null);
        $this->assertFalse($this->bot->login());
    }

    /**
     * @expectedException \Exception
     */
    public function testWrongProvider()
    {
        $this->bot->badProvider;
    }

}
