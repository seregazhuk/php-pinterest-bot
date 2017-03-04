<?php

namespace seregazhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

/**
 * Class BotTest.
 */
class BotTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_should_delegate_calls_to_provider_container()
    {
        /** @var ProvidersContainer $providersContainer */
        $providersContainer = Mockery::mock(ProvidersContainer::class)
            ->shouldReceive('method')
            ->andReturn('OK')
            ->once()
            ->getMock();


        $bot = new Bot($providersContainer);

        $this->assertEquals('OK', $bot->method());
    }
}
