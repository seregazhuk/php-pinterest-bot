<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\tests\Helpers\FollowResponseHelper;
use seregazhuk\PinterestBot\Api\Providers\Interests;

/**
 * Class InterestsTest.
 */
class InterestsTest extends ProviderTest
{
    use FollowResponseHelper;

    /**
     * @var Interests
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Interests::class;

    /** @test */
    public function it_should_follow_interest()
    {
        $interestId = 1111;
        $this->setFollowSuccessResponse($interestId, UrlHelper::RESOURCE_FOLLOW_INTEREST);
        $this->assertTrue($this->provider->follow(1111));

        $this->setFollowErrorResponse($interestId, UrlHelper::RESOURCE_FOLLOW_INTEREST);
        $this->assertFalse($this->provider->follow(1111));
    }

    /** @test */
    public function it_should_unfollow_interest()
    {
        $interestId = 1111;
        $this->setFollowSuccessResponse($interestId, UrlHelper::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertTrue($this->provider->unFollow(1111));

        $this->setFollowErrorResponse($interestId, UrlHelper::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertFalse($this->provider->unFollow(1111));
    }
}
