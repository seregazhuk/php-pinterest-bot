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

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_converts_simple_account_to_a_business_one()
    {
        $provider = $this->getProvider();

        $provider->convertToBusiness('myBusinessName', 'http://example.com');

        $request = [
            'business_name' => 'myBusinessName',
            'website_url'   => 'http://example.com',
            'account_type'  => 'other',
        ];

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS, $request);
    }

    /** @test */
    public function it_confirms_emails()
    {
        $provider = $this->getProvider();
        $provider->confirmEmail('http://some-link-form-email.com');

        $this->assertWasGetRequest('http://some-link-form-email.com');
    }

    /**
     * @return Auth|MockInterface
     */
    protected function getProvider()
    {
        $container = new ProvidersContainer($this->request, new Response());
        return new Auth($container);
    }

    /**
     * @param string $url
     * @param array $data
     */
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
}