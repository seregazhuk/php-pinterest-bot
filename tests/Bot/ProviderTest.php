<?php

namespace seregazhuk\tests\Bot\Api;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;

/**
 * Class ProviderTest.
 */
class ProviderTest extends PHPUnit_Framework_TestCase
{
    use ResponseHelper;

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

        $this->assertEquals($responseData, $provider->visitPage());
    }

    /** @test */
    public function it_should_return_response_object_for_pagination()
    {
        $paginatedResponse = $this->createPaginatedResponse($this->paginatedResponse);

        $request = $this->makeRequest($paginatedResponse, 2);
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

    /**
     * @param mixed $response
     * @param int $times
     * @return Mockery\Mock|Provider|DummyProvider
     */
    protected function makeProvider($response, $times = 1)
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
        $container = new ProvidersContainer($this->makeRequest([]), $response);
        return Mockery::mock(DummyProvider::class, [$container])
            ->makePartial();
    }

    /**
     * @param mixed $response
     * @param int $times
     * @return Mockery\MockInterface|Request
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

class DummyProvider extends Provider {

    /**
     * @param mixed $data
     * @param string $resourceUrl
     * @return \seregazhuk\PinterestBot\Helpers\Pagination
     */
    public function dummyPaginate($data, $resourceUrl)
    {
        return $this->paginate($data, $resourceUrl);
    }

    /**
     * @return array|bool|Response
     */
    public function dummyGet()
    {
        return $this->get([], '');
    }

    /**
     * @return array|bool|Response
     */
    public function dummyPost()
    {
        return $this->post([], '');
    }
}
