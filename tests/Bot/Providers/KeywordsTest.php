<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Keywords;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class KeywordsTest
 * @method Keywords getProvider()
 */
class KeywordsTest extends ProviderBaseTest
{
    /** @test */
    public function it_fetches_recommended_keywords()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn($this->getSearchResponse());

        $recommended = $provider->recommendedFor('johnDoe');

        $request = [
            'scope' => 'pins',
            'query' => 'johnDoe',
        ];

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_SEARCH, $request);
        $this->assertCount(2, $recommended);

        $firstRecommendation = $recommended[0];
        $this->assertEquals('keyword1', $firstRecommendation['term']);
        $this->assertEquals('Some keyword1', $firstRecommendation['display']);
        $this->assertEquals('1', $firstRecommendation['position']);
    }

    /**
     * @return array
     */
    protected function getSearchResponse()
    {
        return [
            'guides' => [
                [
                    'term'     => 'keyword1',
                    'display'  => 'Some keyword1',
                    'position' => 1,
                ],
                [
                    'term'     => 'keyword2',
                    'display'  => 'Some keyword2',
                    'position' => 1,
                ],],
        ];
    }

    protected function getProviderClass()
    {
        return Keywords::class;
    }
}
