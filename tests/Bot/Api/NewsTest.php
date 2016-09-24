<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\Providers\News;

/**
 * Class NewsTest
 * @package seregazhuk\tests\Api
 */
class NewsTest extends ProviderTest
{
    /**
     * @var News
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = News::class;

    /** @test */
    public function it_returns_all_news()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $likes = $this->provider->all();

        $this->assertIsPaginatedResponse($likes);
    }
}