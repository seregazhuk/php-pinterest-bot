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

    protected function getProviderClass()
    {
        return Topics::class;
    }
}
