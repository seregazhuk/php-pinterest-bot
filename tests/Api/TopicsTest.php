<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\Providers\Topics;
use seregazhuk\tests\Helpers\FollowResponseHelper;

/**
 * Class TopicsTest.
 */
class TopicsTest extends ProviderTest
{
    use FollowResponseHelper;

    /**
     * @var Topics
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Topics::class;

    /** @test */
    public function it_should_follow_topic()
    {
        $interestId = 1111;
        $this->setFollowSuccessResponse($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST);
        $this->assertTrue($this->provider->follow($interestId));

        $this->setFollowErrorResponse($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST);
        $this->assertFalse($this->provider->follow($interestId));
    }

    /** @test */
    public function it_should_unfollow_topic()
    {
        $interestId = 1111;
        $this->setFollowSuccessResponse($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertTrue($this->provider->unFollow($interestId));

        $this->setFollowErrorResponse($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertFalse($this->provider->unFollow($interestId));
    }


    /** @test */
    public function it_should_return_topic_info()
    {
        $info = ['name' => 'category1'];

        $response = $this->createApiResponseWithData($info);

        $this->apiShouldReturn($response)
            ->assertEquals($info, $this->provider->getInfo(1));
    }

    /** @test */
    public function it_should_return_generator_for_pins()
    {
        $response = $this->createPaginatedResponse();

        $this->apiShouldReturn($response)
            ->apiShouldReturnEmpty()
            ->assertCount(2, iterator_to_array($this->provider->getPinsFor('test')));
    }
}
