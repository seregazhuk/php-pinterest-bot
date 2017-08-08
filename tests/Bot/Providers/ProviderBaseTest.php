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
    /**
     * @var string
     */
    protected $providerClass = '';

    /**
     * @var Request|MockInterface
     */
    protected $request;

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

    /**
     * @param string $url
     * @param array $requestData
     */
    public function assertWasPostRequest($url, array $requestData = [])
    {
        $postString = Request::createQuery($requestData);

        $this->request
            ->shouldHaveReceived('exec')
            ->withArgs([$url, $postString]);
    }

    public function login()
    {
        $this->request
            ->shouldReceive('isLoggedIn')
            ->andReturn('true');
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
     * @param string $url
     * @param array $data
     */
    public function assertWasGetRequest($url, array $data = [])
    {
        $query = Request::createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->with($url . '?' . $query, '');
    }

    /**
     * @return Provider|MockInterface
     */
    protected function getProvider()
    {
        $container = new ProvidersContainer($this->request, new Response());
        $providerClass = $this->getProviderClass();

        return new $providerClass($container);
    }

    abstract protected function getProviderClass();
}
