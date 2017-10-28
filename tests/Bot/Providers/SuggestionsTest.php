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
        $provider->getForQuery('cats');

        $request = [
            'term'      => 'cats',
            'pin_scope' => 'pins',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_TYPE_AHEAD_SUGGESTIONS, $request);
    }

    protected function getProviderClass()
    {
        return Suggestions::class;
    }
}
