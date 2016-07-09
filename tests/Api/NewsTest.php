<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Api\Providers\News;

/**
 * Class NewsTest.
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
    public function it_should_return_the_latest_user_news()
    {
        $news = ['data' => 'news'];
        $response = $this->createApiResponse($news);
        $error = $this->createErrorApiResponse();

        $this->setResponse($response);
        $this->assertEquals('news', $this->provider->last());

        $this->setResponse($error);
        $this->assertFalse($this->provider->last());
    }
}
