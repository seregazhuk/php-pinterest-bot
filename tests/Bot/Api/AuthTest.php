<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class AuthTest.
 */
class AuthTest extends ProviderTest
{
    /**
     * @var Auth
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Auth::class;

    /**
     * @test
     */
    public function it_should_register_new_user()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->request);

        $this->apiShouldReturnSuccess(6)
            ->assertTrue($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_returns_false_when_error_in_registration()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->request);

        $this->apiShouldReturnError(3)
            ->assertFalse($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_should_register_business_account()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->request);

        $this->apiShouldReturnSuccess(6)
            ->assertTrue($this->provider->registerBusiness('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_should_return_false_when_error_in_business_registration()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->request);

        $this->apiShouldReturnError(3)
            ->assertFalse($this->provider->registerBusiness('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function it_should_throw_exception_when_login_with_credentials()
    {
        $this->setIsLoggedInExpectation(false);
        $this->provider->login('', '');
    }

    /** @test */
    public function it_should_not_make_requests_to_api_when_login_already_logged()
    {
        $this->setIsLoggedInExpectation(true);
        $this->request->shouldNotReceive('exec');

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function it_should_make_api_request_and_clear_token_when_login()
    {
        $this->setIsLoggedInExpectation(false)
            ->apiShouldReturnSuccess();

        $this->request->shouldReceive('getHttpClient')
            ->andReturn(new CurlHttpClient(new Cookies()));

        $this->request
            ->shouldReceive('clearToken')
            ->once();

        $this->request
            ->shouldReceive('login')
            ->once();

        $this->assertTrue($this->provider->login('test', 'test', false));
    }

    /** @test */
    public function it_should_return_false_when_login_fails()
    {
        $this->setIsLoggedInExpectation(false);

        $this->request->shouldReceive('getHttpClient')
            ->andReturn(new CurlHttpClient(new Cookies()));


        $this->apiShouldReturnError();
        $this->request->shouldReceive('clearToken');

        $this->assertFalse($this->provider->login('test', 'test', false));
    }

    /** @test */
    public function it_should_proxy_logout_to_request()
    {
        $this->request->shouldReceive('logout');
        $this->provider->logout();
    }

    /** @test */
    public function is_should_proxy_logged_in_to_request()
    {
        $this->setIsLoggedInExpectation(true);
        $this->assertTrue($this->provider->isLoggedIn());
    }
    /**
     * @param int $times
     */
    protected function setTokenFromCookiesExpectation($times = 1)
    {
        $this->request
            ->shouldReceive('setTokenFromCookies')
            ->times($times)
            ->andReturnSelf();
    }

    /**
     * @param bool $status
     * @return $this
     */
    protected function setIsLoggedInExpectation($status)
    {
        $this->request
            ->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn($status);

        return $this;
    }
}
