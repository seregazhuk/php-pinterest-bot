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
    }

    protected function tearDown()
    {
        Mockery::close();
    }

    protected function assertWasPostRequest($url, array $data = [])
    {
        $postString = Request::createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->withArgs([$url, $postString]);
    }

    /**
     * @param string $url
     * @param array $data
     */
    protected function assertWasGetRequest($url, array $data = [])
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