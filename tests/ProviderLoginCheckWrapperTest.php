<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;
use seregazhuk\PinterestBot\Api\Providers\ProviderLoginCheckWrapper;

class ProviderLoginCheckWrapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * For not logged in request.
     *
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\AuthException
     */
    public function failWhenLoginIsRequired()
    {
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

    /**
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\InvalidRequestException
     */
    public function callNonexistentMethod()
    {
        $provider = new TestProvider(new Request(new CurlAdapter()), new Response());
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $wrapper->badMethod();
    }
}

class TestProvider extends Provider
{
    protected $loginRequiredFor = ['testFail'];

    public function testFail()
    {
        return 'exception';
    }

    public function testSuccess()
    {
        return 'success';
    }
}
