<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\News;

class NewsTest extends ProviderTest
{

    /**
     * @var News
     */
    protected $provider;
    protected $providerClass = News::class;

    /** @test */
    public function getLatest()
    {
        $news = ['data' => 'news'];
        $response = $this->createApiResponse($news);
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(2))->method('exec')->willReturn($error);

        $this->assertEquals('news', $this->provider->latest());
        $this->assertFalse($this->provider->latest());
    }
}
