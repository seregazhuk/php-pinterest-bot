<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\ProviderLoginCheckWrapper;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Exceptions\AuthException;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

class ProviderLoginCheckWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * For not logged in request.
     *
     * @test
     */
    public function failWhenLoginIsRequired()
    {
        $this->expectException(AuthException::class);

        $provider = new TestProvider(new Request(new CurlAdapter()), new Response());
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $wrapper->testFail();
    }

    /** @test */
    public function simpleMethodCall()
    {
        $provider = new TestProvider(new Request(new CurlAdapter()), new Response());
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $this->assertEquals('success', $wrapper->testSuccess());
    }

    /** @test */
    public function callNonexistentMethod()
    {
        $this->expectException(InvalidRequestException::class);
        $provider = new TestProvider(new Request(new CurlAdapter()), new Response());
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $wrapper->badMethod();
    }
}

class TestProvider extends Provider
{
    protected $loginRequired = ['testFail'];

    public function testFail()
    {
        return 'exception';
    }

    public function testSuccess()
    {
        return 'success';
    }
}
