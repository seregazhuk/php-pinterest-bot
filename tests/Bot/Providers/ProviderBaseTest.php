<?php

namespace seregazhuk\tests\Bot\Providers;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

abstract class ProviderBaseTest extends TestCase
{
    use ApiRequestAssertions;

    /**
     * @var string
     */
    protected $providerClass = '';

    protected function setUp()
    {
        parent::setUp();

        $this->request = Mockery::spy(Request::class);

        $this->request
            ->shouldReceive('hasToken')
            ->andReturn(true);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    public function login()
    {
        $this->request
            ->shouldReceive('isLoggedIn')
            ->andReturn(true);
    }

    /**
     * @param mixed $data
     * @param null $times
     * @return $this
     */
    public function pinterestShouldReturn($data, $times = null)
    {
        $response = ['resource_response' => ['data' => $data]];

        $this->request
            ->shouldReceive('exec')
            ->times($times)
            ->andReturn(json_encode($response));

        return $this;
    }

    /**
     * @return Provider|MockInterface
     */
    protected function getProvider()
    {
        $container = new ProvidersContainer($this->request, new Response());
        $providerClassName = $this->getProviderClass();

        return new $providerClassName($container);
    }

    /**
     * @return string
     */
    abstract protected function getProviderClass();
}
