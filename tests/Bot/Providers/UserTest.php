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
        $this->setResponse(['id' => 12345]);

        $provider->deactivate('my reason', 'I want to leave');

        $request = [
            'user_id'     => 12345,
            'reason'      => 'my reason',
            'explanation' => 'I want to leave',
        ];

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DEACTIVATE_ACCOUNT, $request);
    }

    protected function getProviderClass()
    {
        return User::class;
    }
}
