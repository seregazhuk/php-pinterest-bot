<?php

namespace seregazhuk\tests\Bot\Providers\Core;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Exceptions\WrongProvider;
use seregazhuk\PinterestBot\Api\Providers\Core\ProviderWrapper;

/**
 * Class ProvidersContainerTest.
 */
class ProvidersContainerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

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

    protected function setUp()
    {
        $this->response = Mockery::mock(Response::class)->makePartial();
        $this->request = Mockery::mock(Request::class)->makePartial();

        $this->container = new ProvidersContainer(
            $this->request, $this->response
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_provider_wrapper_instance_when_accessing_providers()
    {
        $provider = $this->container->pinners;
        $this->assertInstanceOf(ProviderWrapper::class, $provider);
    }

    /** @test */
    public function it_should_throw_exception_on_getting_wrong_provider()
    {
        $this->expectException(WrongProvider::class);
        $this->container->unknown;
    }

    /** @test */
    public function it_reloads_client_info()
    {
        $clientInfo = ['info'];
        $this->response
            ->shouldReceive('getClientInfo')
            ->once()
            ->andReturn(null);

        $this->request->shouldReceive('exec')->once();

        $this->response
            ->shouldReceive('getClientInfo')
            ->once()
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
