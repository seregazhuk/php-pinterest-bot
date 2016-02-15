<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\News;

/**
 * Class NewsTest
 * @package seregazhuk\tests
 */
class NewsTest extends ProviderTest
{
    /**
     * @var News
     */
    protected $provider;
    /**
     * @var
     */
    protected $providerClass = News::class;

    /** @test */
    public function getLatest()
    {
        $news = ['data' => 'news'];
        $response = $this->createApiResponse($news);
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturn($error);

        $this->assertEquals('news', $this->provider->latest());
        $this->assertFalse($this->provider->latest());
    }
}
