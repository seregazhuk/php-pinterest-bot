<?php

namespace seregazhuk\tests\Bot\Api;

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
        $this->apiShouldFollowTo($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST)
            ->assertTrue($this->provider->follow($interestId));

        $this->apiShouldNotFollow($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST)
            ->assertFalse($this->provider->follow($interestId));
    }

    /** @test */
    public function it_should_unfollow_topic()
    {
        $interestId = 1111;
        $this->apiShouldFollowTo($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST)
            ->assertTrue($this->provider->unFollow($interestId));

        $this->apiShouldNotFollow($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST)
            ->assertFalse($this->provider->unFollow($interestId));
    }


    /** @test */
    public function it_should_return_topic_info()
    {
        $info = ['name' => 'category1'];

        $this->apiShouldReturnData($info)
            ->assertEquals($info, $this->provider->getInfo(1));
    }

    /** @test */
    public function it_should_return_generator_for_pins()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty()
            ->assertIsPaginatedResponse($this->provider->getPinsFor('test'));
    }
}
