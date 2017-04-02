<?php

namespace seregazhuk\tests\Bot\Api;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

/**
 * Class ProviderTest.
 */
class AuthTest extends PHPUnit_Framework_TestCase
{
    use ResponseHelper;

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_accepts_registration_object_on_registration()
    {
        $provider = $this->makeProvider([]);

    }

    /**
     * @param mixed $response
     * @param int $times
     * @return Mockery\Mock|Provider|DummyProvider
     */
    protected function makeProvider($response, $times = 0)
    {
        $request = $this->makeRequest($response, $times);

        return Mockery::mock(Auth::class, [$request, new Response()])
            ->makePartial();
    }

    /**
     * @param mixed $response
     * @param int $times
     * @return Mockery\MockInterface
     */
    protected function makeRequest($response, $times = 1)
    {
        return Mockery::mock(Request::class)
            ->shouldReceive('exec')
            ->times($times)
            ->andReturn(json_encode($response))
            ->getMock();
    }
}
