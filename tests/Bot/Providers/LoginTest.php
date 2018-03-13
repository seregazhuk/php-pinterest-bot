<?php

namespace seregazhuk\tests\Bot\Providers;

use LogicException;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\tests\Helpers\CookiesHelper;

/**
 * Class LoginTest
 * @method Auth getProvider()
 */
class LoginTest extends ProviderBaseTest
{
    use CookiesHelper;

    /** @test */
    public function it_skips_login_if_user_has_auth_cookie()
    {
        $this->createCookieFile(true, 'JohnDoe');
        $provider = $this->getProvider();

        // For resolving logged-in user id
        $this->pinterestShouldReturn(['id' => '12345']);
        $this->request->shouldReceive('autoLogin')->andReturn(true);
        // First check when we call provider's `login` method
        $this->request->shouldReceive('isLoggedIn')->once()->andReturn(false);
        // Second check when provider tries to resolve current user id
        $this->request->shouldReceive('isLoggedIn')->once()->andReturn(true);

        $provider->login('johnDoe', 'secret');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
    }

    /** @test */
    public function it_throws_exception_when_calling_login_without_credentials()
    {
        $provider = $this->getProvider();

        $this->expectException(LogicException::class);

        $provider->login('', '');
    }

    /** @test */
    public function a_user_can_login_with_valid_credentials()
    {
        $provider = $this->getProvider();
        $provider->login('johnDoe', 'secret');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_LOGIN, [
            'username_or_email' => 'johnDoe',
            'password'          => 'secret',
        ]
        );
    }

    /** @test */
    public function it_returns_true_on_success_login()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['some data']);

        $this->assertTrue($provider->login('johnDoe', 'secret'));

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_LOGIN, [
                'username_or_email' => 'johnDoe',
                'password'          => 'secret',
            ]
        );
    }

    /** @test */
    public function it_returns_false_if_login_fails()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn([]);

        $this->assertFalse($provider->login('johnDoe', 'secret'));

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_LOGIN, [
                'username_or_email' => 'johnDoe',
                'password'          => 'secret',
            ]
        );
    }

    protected function getProviderClass()
    {
        return Auth::class;
    }
}
