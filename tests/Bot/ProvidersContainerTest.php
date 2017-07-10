<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;
use seregazhuk\PinterestBot\Api\Providers\Core\ProviderWrapper;

/**
 * Class ProvidersContainerTest.
 */
class ProvidersContainerTest extends TestCase
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
        $this->response = Mockery::mock(Response::class)->makePartial();
        $this->request = Mockery::mock(Request::class)->makePartial();

        $this->container = new ProvidersContainer(
            $this->request, $this->response
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_return_provider_wrapper_instance_when_accessing_providers()
    {
        $provider = $this->container->pinners;
        $this->assertInstanceOf(ProviderWrapper::class, $provider);
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\WrongProvider
     */
    public function it_should_throw_exception_on_getting_wrong_provider()
    {
        $this->container->unknown;
    }

    /** @test */
    public function it_delegates_client_info_to_response()
    {
        $clientInfo = ['info'];
        $this->response
            ->shouldReceive('getClientInfo')
            ->andReturn($clientInfo);

        $this->assertEquals($clientInfo, $this->container->getClientInfo());
    }

    /** @test */
    public function it_can_reload_client_info()
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
    public function it_delegates_last_error_to_response()
    {
        $error = ['message' => 'error'];
        $this->response
            ->shouldReceive('getLastError')
            ->andReturn($error);

        $this->assertEquals($error['message'], $this->container->getLastError());
    }

    /** @test */
    public function it_should_return_last_message_from_response()
    {
        $error = [
            'message' => null,
            'code'    => 'error_code',
        ];

        $this->response
            ->shouldReceive('getLastError')
            ->andReturn($error);

        $this->assertEquals($error['code'], $this->container->getLastError());
    }

    /** @test */
    public function it_should_return_null_if_there_was_no_error_in_response()
    {
        $this->response
            ->shouldReceive('getLastError')
            ->andReturn(false);

        $this->assertNull($this->container->getLastError());
    }

    /** @test */
    public function it_should_return_last_error_code_from_response()
    {
        $error = [
            'message' => 'error_message',
            'code'    => 'error_code',
        ];

        $this->response
            ->shouldReceive('getLastError')
            ->andReturn($error);

        $this->assertEquals($error['message'], $this->container->getLastError());
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
