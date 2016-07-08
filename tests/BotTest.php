<?php

namespace seregazhuk\tests;

use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
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
    public function it_returns_last_error_from_response()
    {
        $error = 'expected_error';
        $mock = Mockery::mock(Response::class)
            ->shouldReceive('getLastError')
            ->andReturn($error)
            ->getMock();

        $request = new Request(new CurlAdapter());
        $providersContainer = new ProvidersContainer($request, $mock);

        $bot = new Bot($providersContainer);

        $this->assertEquals($error, $bot->getLastError());
    }

    /** @test */
    public function it_returns_true_on_success_login()
    {
        $credentials = ['test', 'test'];
        $userProviderMock = Mockery::mock(Pinners::class)
            ->shouldReceive('login')
            ->withArgs($credentials)
            ->andReturn(true)
            ->getMock();

        $containerMock = $this->createContainerMockWithProvider('user', $userProviderMock);

        $bot = new Bot($containerMock);

        $this->assertTrue($bot->login('test', 'test'));
    }

    /** @test */
    public function it_proxies_logout_to_request()
    {
        $userProviderMock = Mockery::mock(Pinners::class)
            ->shouldReceive('logout')
            ->getMock();

        $containerMock = $this->createContainerMockWithProvider('user', $userProviderMock);

        $bot = new Bot($containerMock);

        $bot->logout();
    }

    /** @test */
    public function it_proxies_is_logged_in_to_request()
    {
        $request = Mockery::mock(Request::class)
            ->shouldReceive('isLoggedIn')
            ->andReturn(true)
            ->getMock();

        $providersContainer = new ProvidersContainer($request, new Response());

        $bot = new Bot($providersContainer);

        $this->assertTrue($bot->isLoggedIn());
    }

    /**
     * @param string $providerName
     * @param MockInterface $providerMock
     * @return ProvidersContainer
     */
    protected function createContainerMockWithProvider($providerName, MockInterface $providerMock)
    {
        return Mockery::mock(ProvidersContainer::class)
            ->shouldReceive('getProvider')
            ->with($providerName)
            ->andReturn($providerMock)
            ->getMock();
    }
}
