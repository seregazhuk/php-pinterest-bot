<?php

namespace seregazhuk\tests\Bot\Providers\Core;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

/**
 * Class ProviderTest.
 */
class ProviderTest extends TestCase
{
    use ResponseHelper, MockeryPHPUnitIntegration;

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_returns_data_for_response()
    {
        $response = ['resource_response' => ['data' => 'value']];

        $provider = $this->makeProvider($response);

        $responseData = $response['resource_response']['data'];

        $this->assertEquals($responseData, $provider->dummyGet());
    }

    /** @test */
    public function it_should_return_response_object_for_pagination()
    {
        $paginatedResponse = $this->createPaginatedResponse($this->paginatedResponse);

        $request = $this->makeRequest($paginatedResponse, $times = 2);
        $request
            ->shouldReceive('exec')
            ->once()
            ->andReturn(json_encode([]));

        /** @var DummyProvider $provider */
        $provider = $this->makeProviderWithRequest($request);

        $provider->dummyPaginate(['test' => 'test'], 'http://example.com')->toArray();
    }

    /** @test */
    public function it_should_clear_response_before_pagination()
    {
        /** @var Response $response */
        $response = Mockery::mock(Response::class)
            ->shouldReceive('clear')
            ->once()
            ->getMock()
            ->makePartial();

        /** @var DummyProvider $provider */
        $provider = $this->makeProviderWithResponse($response);

        $provider->dummyPaginate(['test' => 'test'], 'http://example.com')->toArray();
    }

    /** @test */
    public function it_should_return_bool_if_required_for_post_request()
    {
        $response = ['resource_response' => ['data' => 'value']];

        $provider = $this->makeProvider($response);

        $this->assertTrue($provider->dummyPost());
    }

    /** @test */
    public function it_merges_required_login_methods_from_included_traits()
    {
        $provider = $provider = $this->makeProvider($response = [], $times = 0);
        $this->assertTrue($provider->checkMethodRequiresLogin('method1'));
        $this->assertTrue($provider->checkMethodRequiresLogin('method2'));
    }

    /**
     * @param array $response
     * @param int $times
     * @return Mockery\Mock|Provider|DummyProvider
     */
    protected function makeProvider(array $response = [], $times = 1)
    {
        $request = $this->makeRequest($response, $times);

        return $this->makeProviderWithRequest($request);
    }

    /**
     * @param Request $request
     * @return Mockery\Mock|Provider|DummyProvider
     */
    protected function makeProviderWithRequest(Request $request)
    {
        $container = new ProvidersContainer($request, new Response());
        return Mockery::mock(DummyProvider::class, [$container])
            ->makePartial();
    }

    /**
     * @param Response $response
     * @return Mockery\Mock|Provider|DummyProvider
     */
    protected function makeProviderWithResponse(Response $response)
    {
        $container = new ProvidersContainer($this->makeRequest(), $response);
        return Mockery::mock(DummyProvider::class, [$container])
            ->makePartial();
    }

    /**
     * @param array $response
     * @param int $times
     * @return Mockery\MockInterface|Request
     */
    protected function makeRequest(array $response = [], $times = 1)
    {
        return Mockery::mock(Request::class)
            ->makePartial()
            ->shouldReceive('exec')
            ->times($times)
            ->andReturn(json_encode($response))
            ->shouldReceive('hasToken')
            ->andReturn(true)
            ->getMock();
    }
}

class DummyProvider extends Provider {
    use DummyProviderTrait;

    /**
     * @var array
     */
    protected $loginRequiredFor = [
        'method1',
    ];

    /**
     * @param mixed $data
     * @param string $resourceUrl
     * @return Pagination
     */
    public function dummyPaginate($data, $resourceUrl)
    {
        return $this->paginate($resourceUrl, $data);
    }

    /**
     * @return array|bool|Response
     */
    public function dummyGet()
    {
        return $this->get();
    }

    /**
     * @return array|bool|Response
     */
    public function dummyPost()
    {
        return $this->post('');
    }
}

trait DummyProviderTrait {
    /**
     * @return array
     */
    protected function requiresLoginForDummyProviderTrait() {
        return [
            'method2',
        ];
    }
}
