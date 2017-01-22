<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\tests\Helpers\FollowResponseHelper;
use seregazhuk\PinterestBot\Api\Providers\Pinners;

/**
 * Class PinnersTest.
 */
class PinnersTest extends ProviderTest
{
    use FollowResponseHelper;

    /**
     * @var Pinners
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Pinners::class;

    /** @test */
    public function it_should_follow_user()
    {
        $pinnerId = 1;
        $this->apiShouldFollowTo($pinnerId, UrlBuilder::RESOURCE_FOLLOW_USER)
            ->assertTrue($this->provider->follow($pinnerId));

        $this->apiShouldNotFollow($pinnerId, UrlBuilder::RESOURCE_FOLLOW_USER)
            ->assertFalse($this->provider->follow($pinnerId));
    }

    /** @test */
    public function it_should_unfollow_user()
    {
        $pinnerId = 1;
        $this->apiShouldFollowTo($pinnerId, UrlBuilder::RESOURCE_UNFOLLOW_USER)
            ->assertTrue($this->provider->unFollow($pinnerId));

        $this->apiShouldNotFollow($pinnerId, UrlBuilder::RESOURCE_UNFOLLOW_USER)
            ->assertFalse($this->provider->unFollow($pinnerId));
    }

    /** @test */
    public function it_should_return_user_info()
    {
        $userInfo = ['name' => 'test'];
        $this->apiShouldReturnData($userInfo);

        $data = $this->provider->info('username');
        $this->assertEquals($userInfo, $data);
    }

    /** @test */
    public function it_should_return_generator_with_user_followers()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnPagination($response)
            ->assertIsPaginatedResponse($followers = $this->provider->followers('username'))
            ->assertPaginatedResponseEquals($response, $followers);
    }

    /** @test */
    public function it_should_return_generator_with_following_users()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnPagination($response)
            ->assertIsPaginatedResponse($following = $this->provider->following('username'))
            ->assertPaginatedResponseEquals($response, $following);
    }

    /** @test */
    public function it_should_return_generator_with_user_pins()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnPagination($response)
            ->assertIsPaginatedResponse($pins = $this->provider->pins('username', 2))
            ->assertPaginatedResponseEquals($response, $pins);
    }

    /** @test */
    public function it_should_return_generator_when_searching()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnSearchPagination($response)
            ->assertIsPaginatedResponse($res = $this->provider->search('dogs', 2))
            ->assertPaginatedResponseEquals($response, $res);
    }

    /** @test */
    public function it_should_return_generator_with_user_likes()
    {
        $response = $this->paginatedResponse;

        $this->apiShouldReturnPagination($response)
            ->assertIsPaginatedResponse($likes = $this->provider->likes('username'))
            ->assertPaginatedResponseEquals($response, $likes);
    }

    /** @test */
    public function it_should_block_user_by_id()
    {
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->blockById(1111));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->blockById(1111));
    }

    /** @test */
    public function it_proxies_block_by_username_to_block_by_id()
    {
        $this->apiShouldReturnData(['id' => 1111])
            ->apiShouldReturnSuccess()
            ->assertTrue($this->provider->block('test'));
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\WrongFollowingType
     */
    public function it_throws_exception_for_wrong_following_request()
    {
        $this->provider->following('test', 'unknown');
    }
}
