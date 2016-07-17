<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Providers\User;

/**
 * Class UserTest.
 */
class UserTest extends ProviderTest
{
    /**
     * @var User
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = User::class;

    /** @test */
    public function it_should_edit_user_profile()
    {
        $this->setSuccessResponse();
        $attributes = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($attributes));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->profile($attributes));
    }

    /** @test */
    public function it_should_upload_image_when_editing_profile_with_local_image()
    {
        $attributes = [
            'name'          => 'John Doe',
            'profile_image' => 'my_profile_image.jpg'
        ];
        $this->requestMock
            ->shouldReceive('upload')
            ->withArgs([$attributes['profile_image'], UrlHelper::IMAGE_UPLOAD]);
        
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->profile($attributes));
    }

    /**
     * @test
     */
    public function it_should_register_new_user()
    {
        $this->setTokenFromCookiesExpectation(2);
        $this->setProperty('request', $this->requestMock);

        $this->setSuccessResponse(3);
        $this->assertTrue($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_returns_false_when_error_in_registration()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->requestMock);

        $this->setErrorResponse(2);
        $this->assertFalse($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_should_register_business_account()
    {
        $this->setTokenFromCookiesExpectation(2);
        $this->setProperty('request', $this->requestMock);

        $this->setSuccessResponse(3);
        $this->assertTrue($this->provider->registerBusiness('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function it_should_return_false_when_error_in_business_registration()
    {
        $this->setTokenFromCookiesExpectation();
        $this->setProperty('request', $this->requestMock);

        $this->setErrorResponse(2);
        $this->assertFalse($this->provider->registerBusiness('email@email.com', 'test', 'name'));
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
    public function it_should_not_call_requests_to_api_when_login_already_logged()
    {
        $this->setIsLoggedInExpectation(true);
        $this->requestMock->shouldNotReceive('exec');

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function it_should_make_api_request_and_clear_token_when_login()
    {
        $response = $this->createSuccessApiResponse();
        $this->setIsLoggedInExpectation(false);

        $this->setResponse($response);

        $this->requestMock
            ->shouldReceive('clearToken')
            ->once();

        $this->requestMock
            ->shouldReceive('login')
            ->once();

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /**
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\AuthException
     */
    public function it_should_throw_exception_when_login_fails()
    {
        $response = $this->createErrorApiResponse();
        $this->setIsLoggedInExpectation(false);

        $this->setResponse($response);
        $this->requestMock->shouldReceive('clearToken');

        $this->provider->login('test', 'test');
    }

    /** @test */
    public function it_should_proxy_logout_to_request()
    {
        $this->requestMock->shouldReceive('logout');
        $this->provider->logout();
    }

    /** @test */
    public function is_should_proxy_logged_in_to_request()
    {
        $this->setIsLoggedInExpectation(true);
        $this->assertTrue($this->provider->isLoggedIn());
    }

    /** @test */
    public function it_should_convert_simple_account_to_business()
    {
        $success = $this->createSuccessApiResponse();
        $this->setResponse($success);
        $this->assertTrue($this->provider->convertToBusiness('name'));

        $error = $this->createErrorApiResponse();
        $this->setResponse($error);
        $this->assertFalse($this->provider->convertToBusiness('name'));
    }

    protected function setTokenFromCookiesExpectation($times = 1)
    {
        $this->requestMock
            ->shouldReceive('setTokenFromCookies')
            ->times($times)
            ->andReturnSelf();
    }

    protected function setIsLoggedInExpectation($status)
    {
        $this->requestMock
            ->shouldReceive('isLoggedIn')
            ->once()
            ->andReturn($status);
    }
}
