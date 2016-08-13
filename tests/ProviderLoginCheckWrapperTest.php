<?php

namespace seregazhuk\tests;

use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Api\CurlAdapter;
use seregazhuk\PinterestBot\Api\Providers\Provider;
use seregazhuk\PinterestBot\Api\Providers\ProviderLoginCheckWrapper;

class ProviderLoginCheckWrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * For not logged in request.
     *
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\AuthException
     */
    public function it_should_fail_when_login_is_required()
    {
        $provider = new TestProvider(new Request(new CurlAdapter()));
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $wrapper->testFail();
    }

    /** @test */
    public function it_should_call_provider_method()
    {
        $provider = new TestProvider(new Request(new CurlAdapter()));
        $wrapper = new ProviderLoginCheckWrapper($provider);
        $this->assertEquals('success', $wrapper->testSuccess());
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\InvalidRequestException
     */
    public function it_should_throw_exception_when_calling_non_existed_method()
    {
        $provider = new TestProvider(new Request(new CurlAdapter()));
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
