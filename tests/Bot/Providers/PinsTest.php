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

    /** @test */
    public function it_fetches_trending_pins()
    {
        $provider = $this->getProvider();
        $provider->explore($topicId = '12345')->toArray();

        $request = [
            "aux_fields" => [],
            "prepend"    => false,
            "offset"     => 180,
            "section_id" => '12345',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_EXPLORE_PINS, $request);
    }

    /** @test */
    public function it_fetches_visual_similar_pins_for_a_specified_one()
    {
        $provider = $this->getProvider();
        $provider->visualSimilar('12345')->toArray();

        $request = [
            'pin_id'          => '12345',
            // Some magic numbers, I have no idea about them
            'crop'            => [
                "x"                => 0.16,
                "y"                => 0.16,
                "w"                => 0.66,
                "h"                => 0.66,
                "num_crop_actions" => 1,
            ],
            'force_refresh'   => true,
            'keep_duplicates' => false,
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_VISUAL_SIMILAR_PINS, $request);
    }

    /** @test */
    public function it_fetches_related_pins_for_a_specified_one()
    {
        $provider = $this->getProvider();
        $provider->related('12345')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_RELATED_PINS, ['pin' => '12345', 'add_vase' => true]);
    }

    /** @test */
    public function it_returns_a_current_user_feed()
    {
        $provider = $this->getProvider();
        $provider->feed()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_FEED);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Pins::class;
    }
}
