<?php

namespace seregazhuk\tests;

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
     * @var
     */
    protected $providerClass = Interests::class;

    /** @test */
    public function followInterest()
    {
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->follow(1111));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->follow(1111));
    }

    /** @test */
    public function unFollowInterest()
    {
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->unFollow(1111));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->unFollow(1111));
    }
}
