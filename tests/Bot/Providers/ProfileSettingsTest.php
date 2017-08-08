<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

class ProfileSettingsTest extends UserTest
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
}
