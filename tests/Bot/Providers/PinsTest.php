<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PinsTest
 * @method Pins getProvider()
 */
class PinsTest extends ProviderBaseTest
{
    /** @test */
    public function a_user_can_like_a_pin()
    {
        $provider = $this->getProvider();
        $provider->like('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_LIKE_PIN, ['pin_id' => '12345']);
    }

    /** @test */
    public function a_user_can_dislike_a_pin()
    {
        $provider = $this->getProvider();
        $provider->unLike('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_UNLIKE_PIN, ['pin_id' => '12345']);
    }

    /** @test */
    public function it_retrieves_detailed_info_for_a_pin()
    {
        $provider = $this->getProvider();
        $provider->info('12345');

        $request = [
            'id'            => '12345',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, $request);
    }

    /** @test */
    public function it_fetches_pins_for_a_specified_source()
    {
        $provider = $this->getProvider();
        $provider->fromSource('http://flickr.com')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_DOMAIN_FEED, ['domain' => 'http://flickr.com']);
    }

    /** @test */
    public function it_fetches_users_activity_for_a_specified_pin()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['aggregated_pin_data' => ['id' => '123456']]);

        $provider->activity('123456')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_ACTIVITY, ['aggregated_pin_data_id' => '123456']);
    }

    /** @test */
    public function it_deletes_a_pin()
    {
        $provider = $this->getProvider();
        $provider->delete('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DELETE_PIN, ['id' => '12345']);
    }

    /** @test */
    public function it_fetches_analytics_about_a_pin()
    {
        $provider = $this->getProvider();
        $provider->analytics('12345');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_ANALYTICS, ['pin_id' => '12345']);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Pins::class;
    }
}
