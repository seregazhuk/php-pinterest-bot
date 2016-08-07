<?php

namespace seregazhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

/**
 * Class ProvidersContainerTest.
 */
class ProvidersContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ProvidersContainer
     */
    private $container;

    public function setUp()
    {
        $response = Mockery::mock(Response::class);
        $request = Mockery::mock(Request::class);
        $this->container = new ProvidersContainer($request, $response);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_provider_instance()
    {
        $provider = $this->container->getProvider('pinners');
        $this->assertNotEmpty($provider);
    }

    /** @test */
    public function it_should_return_request_instance()
    {
        $this->assertInstanceOf(Request::class, $this->container->getRequest());
    }

    /** @test */
    public function it_should_return_response_instance()
    {
        $this->assertInstanceOf(Response::class, $this->container->getResponse());
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\WrongProviderException
     */
    public function it_should_throw_exception_on_getting_wrong_provider()
    {
        $this->container->getProvider('unknown');
    }
}
