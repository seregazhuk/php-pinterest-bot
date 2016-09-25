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
    public function it_should_return_last_error_from_response()
    {
        $error = ['message' => 'expected_error'];
        $response = Mockery::mock(Response::class)
            ->shouldReceive('getLastError')
            ->andReturn($error)
            ->getMock();

        $request = new Request(new CurlHttpClient(new Cookies()));

        $providersContainer = new ProvidersContainer($request, $response);

        $bot = new Bot($providersContainer);

        $this->assertEquals($error['message'], $bot->getLastError());
    }
}
