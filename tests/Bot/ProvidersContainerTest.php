<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Helpers\Cookies;

/**
 * Class ProvidersContainerTest.
 */
class ProvidersContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ProvidersContainer
     */
    protected $container;

    /**
     * @var Response|MockInterface
     */
    protected $response;

    /**
     * @var Request|MockInterface
     */
    protected $request;

    public function setUp()
    {
        $this->response = Mockery::mock(Response::class);
        $this->request = Mockery::mock(Request::class);

        $this->container = new ProvidersContainer(
            $this->request, $this->response
        );
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

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\WrongProvider
     */
    public function it_should_throw_exception_on_getting_wrong_provider()
    {
        $this->container->getProvider('unknown');
    }

    /** @test */
    public function it_should_proxy_client_info_to_response()
    {
        $clientInfo = ['info'];
        $this->response
            ->shouldReceive('getClientInfo')
            ->andReturn($clientInfo);

        $this->assertEquals($clientInfo, $this->container->getClientInfo());
    }

    /** @test */
    public function it_returns_http_client_instance()
    {
        $this->request
            ->shouldReceive('getHttpClient')
            ->andReturn(new CurlHttpClient(new Cookies()));

        $this->assertInstanceOf(HttpClient::class, $this->container->getHttpClient());
    }
}
