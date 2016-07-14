<?php

namespace szhuk\tests\Factories;

use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Factories\PinterestBot;

class PinterestBotTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_should_return_an_instance_of_bot()
    {
        $this->assertInstanceOf(Bot::class, PinterestBot::create());
    }
}
