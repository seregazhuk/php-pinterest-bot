<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Exceptions\WrongFollowingType;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PinnersTest
 * @method Pinners getProvider()
 */
class PinnersTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_info_by_username()
    {
        $provider = $this->getProvider();
        $provider->info('johnDoe');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_INFO, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_can_block_user_by_name()
    {
        $provider = $this->getProvider();

        // Used to resolve a user by name
        $this->pinterestShouldReturn(['id' => '12345']);

        $provider->block('johnDoe');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_BLOCK_USER, ['blocked_user_id' => '12345']);
    }

    /** @test */
    public function it_fetches_user_info_to_block_a_user_by_name()
    {
        $provider = $this->getProvider();
        $provider->block('johnDoe');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_INFO, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_following_people_for_a_specified_user()
    {
        $provider = $this->getProvider();
        $provider->followingPeople('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_FOLLOWING, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_following_boards_for_a_specified_user()
    {
        $provider = $this->getProvider();
        $provider->followingBoards('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_FOLLOWING_BOARDS, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_following_interests_for_a_specified_user()
    {
        $provider = $this->getProvider();
        $provider->followingInterests('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_FOLLOWING_INTERESTS, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_pins_for_a_specified_user()
    {
        $provider = $this->getProvider();
        $provider->pins('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_PINS, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_likes_for_a_specified_user()
    {
        $provider = $this->getProvider();
        $provider->likes('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_LIKES, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_pins_that_a_specified_user_has_tried()
    {
        $provider = $this->getProvider();
        $provider->tried('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_TRIED, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_a_specified_user_followers()
    {
        $provider = $this->getProvider();
        $provider->followers('johnDoe')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_FOLLOWERS, ['username' => 'johnDoe']);
    }

    /** @test */
    public function it_fetches_followers_for_a_current_user()
    {
        $provider = $this->getProvider();

        $this->login();
        $this->pinterestShouldReturn(['username' => 'johnDoe']);
        $provider->followers()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_USER_FOLLOWERS, ['username' => 'johnDoe']);
    }

    /** @test */
    public function a_user_can_follow_another_user()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['id' => '12345']);

        $provider->follow('johnDoe');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_FOLLOW_USER, ['user_id' => '12345']);
    }

    /** @test */
    public function a_user_can_unfollow_another_user()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['id' => '12345']);

        $provider->unFollow('johnDoe');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_UNFOLLOW_USER, ['user_id' => '12345']);
    }

    /** @test */
    public function it_throws_exception_when_trying_to_fetching_unknown_following_entities()
    {
        $provider = $this->getProvider();
        $this->expectException(WrongFollowingType::class);
        $provider->following('johnDoe', 'UNKNOWN');
    }

    protected function getProviderClass()
    {
        return Pinners::class;
    }
}
