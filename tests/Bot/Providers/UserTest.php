<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Forms\Profile;
use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class UserTest
 * @method User getProvider()
 */
class UserTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_current_user_profile_data()
    {
        $provider = $this->getProvider();
        $provider->profile();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
    }

    /** @test */
    public function it_updates_current_user_profile()
    {
        $provider = $this->getProvider();
        $provider->profile(['name' => 'test']);

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS, ['name' => 'test']
        );
    }

    /** @test */
    public function it_accepts_a_form_object_for_changing_profile()
    {
        $provider = $this->getProvider();
        $profile = (new Profile())
            ->setFirstName('my name')
            ->setLastName('last name');

        $provider->profile($profile);
        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_UPDATE_USER_SETTINGS,
            $profile->toArray()
        );
    }

    /** @test */
    public function it_deactivates_current_user()
    {
        $provider = $this->getProvider();

        // To resolve a current user id
        $this->pinterestShouldReturn(['id' => 12345]);

        $provider->deactivate('my reason', 'I want to leave');

        $request = [
            'user_id'     => 12345,
            'reason'      => 'my reason',
            'explanation' => 'I want to leave',
        ];

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT, $request);
    }

    /** @test */
    public function it_invites_new_users_by_email()
    {
        $provider = $this->getProvider();
        $provider->invite('johnDoe@example.com');

        $request = ['email' => 'johnDoe@example.com', 'type' => 'email'];

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_INVITE,
            $request
        );
    }

    /** @test */
    public function it_clears_current_user_search_history()
    {
        $provider = $this->getProvider();
        $provider->clearSearchHistory();

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CLEAR_SEARCH_HISTORY);
    }

    /** @test */
    public function it_fetches_current_user_name()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['username' => 'johnDoe']);

        $this->assertEquals('johnDoe', $provider->username());
    }

    /** @test */
    public function it_fetches_current_user_id()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['id' => '12345']);

        $this->assertEquals('12345', $provider->id());
    }

    /** @test */
    public function it_fetches_current_user_ban_status()
    {
        $provider = $this->getProvider();

        $this->pinterestShouldReturn(['is_write_banned' => true], $times = 1);
        $this->assertTrue($provider->isBanned());

        $this->pinterestShouldReturn(['is_write_banned' => false], $times = 1);
        $this->assertFalse($provider->isBanned());
    }

    protected function getProviderClass()
    {
        return User::class;
    }
}
