<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
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
        $this->setFollowSuccessResponse($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST);
        $this->assertTrue($this->provider->follow($interestId));

        $this->setFollowErrorResponse($interestId, UrlBuilder::RESOURCE_FOLLOW_INTEREST);
        $this->assertFalse($this->provider->follow($interestId));
    }

    /** @test */
    public function it_should_unfollow_interest()
    {
        $interestId = 1111;
        $this->setFollowSuccessResponse($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertTrue($this->provider->unFollow($interestId));

        $this->setFollowErrorResponse($interestId, UrlBuilder::RESOURCE_UNFOLLOW_INTEREST);
        $this->assertFalse($this->provider->unFollow($interestId));
    }

    /** @test */
    public function it_should_return_main_categories()
    {
        $categories = ['category1', 'category2'];

        $response = $this->createApiResponse(['data' => $categories]);

        $this->setResponseExpectation($response);

        $this->assertEquals($categories, $this->provider->getMain());
    }

    /** @test */
    public function it_should_return_category_info()
    {
        $info = ['name' => 'category1'];

        $response = $this->createApiResponse(['data' => $info]);

        $this->setResponseExpectation($response);

        $this->assertEquals($info, $this->provider->getInfo(1));
    }
}
