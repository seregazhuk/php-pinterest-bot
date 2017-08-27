<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Interests;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class InterestsTest
 * @method Interests getProvider()
 */
class InterestsTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_main_interests()
    {
        $provider = $this->getProvider();
        $provider->main();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CATEGORIES, ['category_types' => 'main']);
    }

    /** @test */
    public function it_returns_info_for_a_specified_category()
    {
        $provider = $this->getProvider();
        $provider->info('some category');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CATEGORY, ['category' => 'some category']);
    }

    /** @test */
    public function it_returns_related_topics_for_a_specified_interest()
    {
        $provider = $this->getProvider();
        $provider->getRelatedTopics('interest-name');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CATEGORIES_RELATED, ['interest_name' => 'interest-name']);
    }

    /** @test */
    public function it_fetches_pins_for_a_specified_interest()
    {
        $provider = $this->getProvider();
        $provider->pins('some-interest')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CATEGORY_FEED, [
            'feed'             => 'some-interest',
            'is_category_feed' => true,
        ]);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Interests::class;
    }
}
