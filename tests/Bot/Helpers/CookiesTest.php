<?php

namespace szhuk\tests\Bot\Helpers;

use PHPUnit_Framework_TestCase;
use seregazhuk\tests\Helpers\CookiesHelper;
use seregazhuk\PinterestBot\Helpers\Cookies;

class CookiesTest extends PHPUnit_Framework_TestCase
{
    use CookiesHelper;

    /**
     * @var string
     */
    protected $cookieFileName = 'cookies.txt';

    /** @test */
    public function it_fills_cookies_from_file()
    {
        $cookies = new Cookies();
        $cookies->fill($this->cookieFileName);

        $parsedCookies = $cookies->all();

        $this->assertNotEmpty($parsedCookies);
        $this->assertArrayHasKey('csrftoken', $parsedCookies);
    }

    /** @test */
    public function it_returns_null_if_cookies_does_not_exist()
    {
        $cookies = new Cookies();

        $cookies->fill($this->cookieFileName);

        $this->assertNull($cookies->get('unknown'));
    }

    /** @test */
    public function it_returns_cookie_value()
    {
        $cookies = new Cookies();

        $cookies->fill($this->cookieFileName);

        $this->assertEquals('123456', $cookies->get('csrftoken'));
    }

    /** @test */
    public function it_returns_csrf_token()
    {
        $cookies = new Cookies();

        $cookies->fill($this->cookieFileName);

        $this->assertEquals('123456', $cookies->getToken());
    }

    protected function setUp()
    {
        $this->createCookieFile($this->cookieFileName);
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        unlink($this->cookieFileName);
        parent::tearDown();
    }
}