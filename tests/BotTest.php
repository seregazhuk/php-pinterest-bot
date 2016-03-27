<?php

namespace seregazhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

/**
 * Class BotTest.
 */
class BotTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function getLastResponseError()
    {
        $error = 'expected_error';
        $mock = Mockery::mock(Response::class)->shouldReceive('getLastError')->andReturn($error)->getMock();

        $request = new Request(new CurlAdapter());
        $providersContainer = new ProvidersContainer($request, $mock);

        $bot = new Bot($providersContainer);

        $this->assertEquals($error, $bot->getLastError());
    }
}
