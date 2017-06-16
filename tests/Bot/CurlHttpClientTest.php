<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\tests\Helpers\CookiesHelper;
use seregazhuk\PinterestBot\Api\CurlHttpClient;

/**
 * Class RequestTest.
 */
class CurlHttpClientTest extends PHPUnit_Framework_TestCase
{
    use CookiesHelper;

    protected function tearDown()
    {
        Mockery::close();
    }

    /** @test */
    public function it_should_remove_cookies_file()
    {
        $client = new CurlHttpClient(new Cookies());

        $this->createCookieFile(true, 'test_name');
        $this->assertTrue(file_exists($this->cookieFilePath));

        $client->loadCookies('test_name');

        $client->removeCookies();

        $this->assertFalse(file_exists($this->cookieFilePath));
    }

    /** @test */
    public function it_creates_cookies_file_if_doesnt_exist()
    {
        $client = new CurlHttpClient(new Cookies());
        $client->loadCookies('test_name');

        $this->assertTrue(file_exists($this->getCookiePath('test_name')));
    }

    /** @test */
    public function it_doesnt_create_a_cookie_file_for_anonymous_requests()
    {
        /** @var Mockery\MockInterface|CurlHttpClient $client */
        $client = Mockery::mock(CurlHttpClient::class, [new Cookies()])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldNotReceive('initCookieFile')
            ->getMock();

        $client->loadCookies();
    }


    /** @test */
    public function it_can_use_proxy_settings()
    {
        $client = new CurlHttpClient(new Cookies());

        $this->assertFalse($client->usesProxy());

        $client->useProxy('192.168.1.1', '1235');

        $this->assertTrue($client->usesProxy());
    }

    /** @test */
    public function it_removes_proxy_settings()
    {
        $client = new CurlHttpClient(new Cookies());

        $client->useProxy('192.168.1.1', '1235');

        $client->dontUseProxy();

        $this->assertFalse($client->usesProxy());
    }
}
