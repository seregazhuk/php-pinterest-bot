<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Suggestions;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * @method Suggestions getProvider()
 */
class SuggestionsTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_suggestions_for_a_specified_search_query()
    {
        $provider = $this->getProvider();
        $provider->searchFor('cats');

        $request = [
            'term'      => 'cats',
            'pin_scope' => 'pins',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_TYPE_AHEAD_SUGGESTIONS, $request);
    }

    /** @test */
    public function it_returns_suggestions_for_a_specified_tag_query()
    {
        $provider = $this->getProvider();
        $provider->tagsFor('cats');

        $request = [
            'query'      => '#cats',
            'showPinCount' => true,
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_HASHTAG_TYPE_AHEAD_SUGGESTIONS, $request);
    }

    protected function getProviderClass()
    {
        return Suggestions::class;
    }
}
