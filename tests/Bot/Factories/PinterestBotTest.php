<?php

namespace szhuk\tests\Bot\Factories;

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

    /** @test */
    public function it_has_private_constructor()
    {
        $reflection = new \ReflectionClass(PinterestBot::class);
        $constructor = $reflection->getConstructor();
        $this->assertFalse($constructor->isPublic());
    }

    /** @test */
    public function it_has_private_clone_method()
    {
        $reflection = new \ReflectionClass(PinterestBot::class);
        $constructor = $reflection->getMethod('__clone');
        $this->assertFalse($constructor->isPublic());
    }
}
