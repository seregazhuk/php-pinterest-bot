<?php

namespace seregazhuk\tests\Bot\Api;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\Traits\ResolvesCurrentUser;

/**
 * Class ProviderTest.
 */
class ResolvesCurrentUsernameTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_calls_user_provider_to_get_current_user_name()
    {
        $username = 'John Doe';

        $provider = $this->makeProvider($this->makeRequest($username));

        $this->assertEquals($username, $provider->getCurrentUserName());
    }

    /**
     * @param Request $request
     * @return DummyUsernameProvider
     */
    protected function makeProvider(Request $request)
    {
        $container = new ProvidersContainer($request, new Response());

        /** @var DummyUsernameProvider $provider */
        $provider = Mockery::mock(DummyUsernameProvider::class, [$container])->makePartial();

        return $provider;
    }

    /**
     * @param string $username
     * @return MockInterface|Request
     */
    protected function makeRequest($username)
    {
        /** @var Request $request */
        $request = Mockery::mock(Request::class)
            ->makePartial()
            ->shouldReceive('exec')
            ->andReturn(json_encode($this->makePinterestProfileResponse($username)))
            ->getMock();

        $request->shouldReceive('isLoggedIn')->andReturn(true);

        return $request;
    }

    /**
     * @param $username
     * @return array
     */
    protected function makePinterestProfileResponse($username)
    {
        return ['resource_response' => ['data' => ['username' => $username]]];
    }
}


class DummyUsernameProvider extends Provider {

    use ResolvesCurrentUser;

    /**
     * @return array|bool|Response
     */
    public function getCurrentUserName()
    {
        return $this->resolveCurrentUsername();
    }
}
