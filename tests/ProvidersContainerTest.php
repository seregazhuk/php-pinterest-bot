<?php

namespace seregazhuk\tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Exceptions\WrongProviderException;

/**
 * Class ProvidersContainerTest
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
    public function getValidProvider()
    {
        $provider = $this->container->getProvider('pinners');
        $this->assertNotEmpty($provider);
    }

    /** @test */
    public function getRequest()
    {
        $this->assertInstanceOf(Request::class, $this->container->getRequest());
    }

    /** @test */
    public function getWrongProvider()
    {
        $this->expectException(WrongProviderException::class);
        $this->container->getProvider('unknown');
    }
}
