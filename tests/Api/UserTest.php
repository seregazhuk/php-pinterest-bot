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
    protected $providerClass = User::class;

    /** @test */
    public function editProfile()
    {
        $this->setSuccessResponse();
        $attributes = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($attributes));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->profile($attributes));
    }

    /** @test */
    public function editProfileWithImage()
    {
        $attributes = [
            'name'          => 'John Doe',
            'profile_image' => 'my_profile_image.jpg'
        ];
        $this->requestMock->shouldReceive('upload')->withArgs(
                [
                    $attributes['profile_image'],
                    UrlHelper::IMAGE_UPLOAD
                ]
            );
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->profile($attributes));
    }

    /**
     * @test
     */
    public function registerReturnsTrueOnSuccess()
    {
        $this->requestMock->shouldReceive('setTokenFromCookies')->twice()->andReturnSelf();

        $this->setProperty('request', $this->requestMock);

        $this->setSuccessResponse(3);
        $this->assertTrue($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     */
    public function registerReturnsFalseOnFail()
    {
        $this->requestMock->shouldReceive('setTokenFromCookies')->once()->andReturnSelf();

        $this->setProperty('request', $this->requestMock);

        $this->setErrorResponse(2);
        $this->assertFalse($this->provider->register('email@email.com', 'test', 'name'));
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function loginWithEmptyCredentials()
    {
        $this->requestMock->shouldReceive('isLoggedIn')->once()->andReturn(false);
        $this->provider->login('', '');
    }

    /** @test */
    public function loginWhenAlreadyLogged()
    {
        $this->requestMock->shouldReceive('isLoggedIn')->once()->andReturn(true);
        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function successLogin()
    {
        $response = $this->createSuccessApiResponse();
        $this->requestMock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->requestMock->shouldReceive('exec')->andReturn($response);
        $this->requestMock->shouldReceive('clearToken')->once();
        $this->requestMock->shouldReceive('login')->once();

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /**
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\AuthException
     */
    public function loginFails()
    {
        $response = $this->createErrorApiResponse();
        $this->requestMock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->requestMock->shouldReceive('exec')->andReturn($response);
        $this->requestMock->shouldReceive('clearToken');

        $this->provider->login('test', 'test');
    }

    /** @test */
    public function logout()
    {
        $this->requestMock->shouldReceive('logout');
        $this->provider->logout();
    }

    /** @test */
    public function isLoggedIn()
    {
        $this->requestMock->shouldReceive('isLoggedIn')->andReturn(true)->getMock();

        $this->assertTrue($this->provider->isLoggedIn());
    }
}
