<?php

namespace seregazhuk\tests;

use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Bot;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Helpers\Cookies;

/**
 * Class BotTest.
 */
class BotTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_should_return_last_error_from_response()
    {
        $error = ['message' => 'expected_error'];
        $request = Mockery::mock(Request::class, [new CurlHttpClient(new Cookies())])
            ->shouldReceive('getLastError')
            ->andReturn($error)
            ->getMock();

        $providersContainer = new ProvidersContainer($request, new Response());

        $bot = new Bot($providersContainer);

        $this->assertEquals($error['message'], $bot->getLastError());
    }


    /**
     * @param string $providerName
     * @param MockInterface $providerMock
     * @return ProvidersContainer
     */
    protected function get_container_with_expected_provider($providerName, MockInterface $providerMock)
    {
        return Mockery::mock(ProvidersContainer::class)
            ->shouldReceive('getProvider')
            ->with($providerName)
            ->andReturn($providerMock)
            ->getMock();
    }
}
