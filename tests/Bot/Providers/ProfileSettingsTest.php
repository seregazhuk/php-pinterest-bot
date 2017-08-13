<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class ProfileSettingsTest
 * @method User getProvider()
 */
class ProfileSettingsTest extends ProviderBaseTest
{
    /** @test */
    public function it_retrieves_current_user_sessions_history()
    {
        $provider = $this->getProvider();
        $provider->sessionsHistory();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_SESSIONS_HISTORY);
    }

    /** @test */
    public function it_returns_list_of_available_locales()
    {
        $provider = $this->getProvider();
        $provider->getLocales();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_AVAILABLE_LOCALES);
    }

    /** @test */
    public function it_returns_list_of_available_countries()
    {
        $provider = $this->getProvider();
        $provider->getCountries();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_AVAILABLE_COUNTRIES);
    }

    /** @test */
    public function it_returns_list_of_available_account_types()
    {
        $provider = $this->getProvider();
        $provider->getAccountTypes();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_AVAILABLE_ACCOUNT_TYPES);
    }

    protected function getProviderClass()
    {
        return User::class;
    }
}
