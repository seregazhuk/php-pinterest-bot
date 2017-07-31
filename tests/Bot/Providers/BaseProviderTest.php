<?php

namespace seregazhuk\tests\Bot\Providers;

use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;

abstract class BaseProviderTest extends TestCase
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
        $this->request->shouldReceive('hasToken')->andReturn(true);
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $expectsJson
     */
    public function assertWasPostRequest($url, array $data = [], $expectsJson = true)
    {
        $postString = Request::createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->withArgs([$url, $postString, $expectsJson]);
    }

    public function login()
    {
        $this->request
            ->shouldReceive('isLoggedIn')
            ->andReturn('true');
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function pinterestShouldReturn($data)
    {
        $response = ['resource_response' => ['data' => $data]];

        $this->request
            ->shouldReceive('exec')
            ->andReturn(json_encode($response));

        return $this;
    }

    /**
     * @param string $url
     * @param array $data
     * @param bool $expectsJson
     */
    public function assertWasGetRequest($url, array $data = [], $expectsJson = true)
    {
        $query = Request::createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->with($url . '?' . $query, '', $expectsJson);
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

    /**
     * @param array $data
     */
    protected function setResponse(array $data)
    {
        $response = ['resource_response' => ['data' => $data]];
        $this->request
            ->shouldReceive('exec')
            ->andReturn(json_encode($response));
    }



    abstract protected function getProviderClass();
}
