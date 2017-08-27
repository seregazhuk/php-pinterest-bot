<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Password;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PasswordTest
 * @method Password getProvider()
 */
class PasswordTest extends ProviderBaseTest
{
    /** @test */
    public function it_sends_a_password_reset_link()
    {
        $provider = $this->getProvider();
        $provider->sendResetLink('username');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_RESET_PASSWORD_SEND_LINK,
            ['username_or_email' => 'username']
        );
    }

    /** @test */
    public function it_can_change_password_for_a_current_user()
    {
        $provider = $this->getProvider();
        $provider->change('my-old-password', 'new-password');

        $request = [
            'old_password' => 'my-old-password',
            'new_password' => 'new-password',
            'new_password_confirm' => 'new-password',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CHANGE_PASSWORD, $request);
    }

    /** @test */
    public function it_returns_false_when_reset_password_with_invalid_link()
    {
        $provider = $this->getProvider();
        $this->assertFalse($provider->reset('http://example.com', 'newPassword'));
    }


    /** @test */
    public function it_restores_a_password_by_a_link_from_email()
    {
        $provider = $this->getProvider();
        $this->request
            ->shouldReceive('getCurrentUrl')
            ->andReturn('http://example.com/some-path/?t=token&e=12345');

        $this->pinterestShouldReturn(true);

        $this->assertTrue($provider->reset('http://example.com/some-path/?t=token&e=12345', 'newPassword'));
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Password::class;
    }
}
