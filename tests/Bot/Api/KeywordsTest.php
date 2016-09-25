<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\Providers\Keywords;

/**
 * Class KeywordsTest
 *
 * @package seregazhuk\tests
 */
class KeywordsTest extends ProviderTest
{
    /**
     * @var Keywords
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Keywords::class;

    /** @test */
    public function it_should_return_recommended_keywords()
    {
        $recommendation = [
            'someData' => 'data',
            'term'     => 'term1',
            'display'  => 'Term 1',
            'position' => 0,
        ];
        $this->apiShouldReturnData(['guides' => [$recommendation]]);

        $result = $this->provider->recommendedFor('test');

        $expected = [
            'term'     => 'term1',
            'display'  => 'Term 1',
            'position' => 0,
        ];
        $this->assertEquals([$expected], $result);
    }

    /** @test */
    public function it_should_return_empty_array_for_no_recommendations()
    {
        $this->apiShouldReturnData('')
            ->assertEmpty($this->provider->recommendedFor('test'));
    }
}