<?php

namespace seregazhuk\tests\Bot\Providers;

use Mockery;
use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use Mockery\MockInterface;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class AuthTest extends TestCase
{
    /**
     * @var Request|MockInterface
     */
    protected $request;

    protected function setUp()
    {
        parent::setUp();
        $this->request = Mockery::spy(Request::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_converts_simple_account_to_a_business_one()
    {
        $provider = $this->makeProvider();

        $provider->convertToBusiness('myBusinessName', 'http://example.com');

        $request = [
            'business_name' => 'myBusinessName',
            'website_url'   => 'http://example.com',
            'account_type'  => 'other',
        ];

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS, $request);
    }

    protected function makeRequest($response = [])
    {
        return Mockery::spy(Request::class)
          ->shouldReceive('exec')
          ->andReturn(json_encode($response))
          ->andReturn(true)
          ->shouldReceive('hasToken')
          ->andReturn(true)
          ->getMock();
    }

    /**
     * @return Auth|MockInterface
     */
    protected function makeProvider()
    {
        $container = new ProvidersContainer($this->request, new Response());
        return new Auth($container);
    }

    /**
     * @param $url
     * @param $data
     */
    protected function assertWasPostRequest($url, $data)
    {
        $postString = Request::createQuery($data);

        $this->request
            ->shouldHaveReceived('exec')
            ->withArgs([$url, $postString]);
    }
}