<?php

namespace seregazhuk\tests\Bot\Providers\Core;

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Response;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\ProvidersContainer;
use seregazhuk\PinterestBot\Exceptions\AuthRequired;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;
use seregazhuk\PinterestBot\Api\Providers\Core\Provider;
use seregazhuk\PinterestBot\Api\Providers\Core\ProviderWrapper;

class ProviderWrapperTest extends TestCase
{
    /** @test */
    public function it_should_fail_when_login_is_required()
    {
        $this->expectException(AuthRequired::class);

        $wrapper = $this->createWrapper();
        $wrapper->testFail();
    }

    /** @test */
    public function it_should_call_provider_method()
    {
        $wrapper = $this->createWrapper();
        $this->assertEquals('success', $wrapper->testSuccess());
    }

    /** @test */
    public function it_should_throw_exception_when_calling_non_existed_method()
    {
        $this->expectException(InvalidRequest::class);
        $this->createWrapper()->badMethod();
    }

    /**
     * @return ProviderWrapper
     */
    protected function createWrapper()
    {
        $request = new Request(new CurlHttpClient(new Cookies()));

        $provider = new TestProvider(new ProvidersContainer($request, new Response()));

        return new ProviderWrapper($provider);
    }
}

/**
 * Dummy Class TestProvider
 */
class TestProvider extends Provider
{
    /**
     * @var array
     */
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
