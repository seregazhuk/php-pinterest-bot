<?php

namespace seregazhuk\tests\Api;

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
    protected $providerClass = Keywords::class;

    /** @test */
    public function recommendedFor()
    {
        $query = 'test';
        $recommendation = [
            'someData' => 'data',
            'term'     => 'term1',
            'display'  => 'Term 1',
            'position' => 0,
        ];
        $this->setResponse(
            [
                'resource_response' => [
                    'data' => ['guides' => [$recommendation]]
                ]
            ]
        );

        $result = $this->provider->recommendedFor($query);

        $expected = [
            'term'     => 'term1',
            'display'  => 'Term 1',
            'position' => 0,
        ];
        $this->assertEquals([$expected], $result);
    }

    /** @test */
    public function emptyResultsForRecommendedFor()
    {
        $query = 'test';
        $this->setResponse(['resource_response' => ['data' => '']]);

        $this->assertEmpty($this->provider->recommendedFor($query));
    }
}