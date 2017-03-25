<?php

namespace seregazhuk\tests\Bot;

use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\tests\Helpers\CookiesHelper;

/**
 * Class RequestTest.
 */
class CurlHttpClientTest extends PHPUnit_Framework_TestCase
{
    use CookiesHelper;

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
}
