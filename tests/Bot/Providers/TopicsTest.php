<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Topics;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class TopicsTest
 * @method Topics getProvider()
 */
class TopicsTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_info_for_a_specified_topic()
    {
        $provider = $this->getProvider();
        $provider->info('topic');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_TOPIC, ['interest' => 'topic']);
    }

    /** @test */
    public function it_returns_an_array_of_trending_topics()
    {
        $provider = $this->getProvider();
        $provider->explore();

        $request = [
            'aux_fields' => [],
            'offset' => 180,
        ];

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_EXPLORE_SECTIONS, $request);
    }

    /** @test */
    public function it_returns_related_topics_for_a_specified_topic()
    {
        $provider = $this->getProvider();
        $provider->getRelatedTopics('topic-name');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CATEGORIES_RELATED, ['interest_name' => 'topic-name']);
    }

    /** @test */
    public function it_fetches_pins_for_a_specified_topic()
    {
        $provider = $this->getProvider();
        $provider->pins('topic-name')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_TOPIC_FEED, [
            'interest'  => 'topic-name',
            'pins_only' => false,
        ]);
    }

    /** @test */
    public function a_user_can_follow_a_topic()
    {
        $provider = $this->getProvider();
        $provider->follow('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_FOLLOW_INTEREST,
            [
                'interest_id' => '12345',
                'interest_list' => 'favorited'
            ]);
    }

    /** @test */
    public function a_user_can_unfollow_a_topic()
    {
        $provider = $this->getProvider();
        $provider->unFollow('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_UNFOLLOW_INTEREST,
            [
                'interest_id' => '12345',
                'interest_list' => 'favorited'
            ]);
    }

    protected function getProviderClass()
    {
        return Topics::class;
    }
}
