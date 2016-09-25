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
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $followers = $this->provider->followers('username');
        $this->assertIsPaginatedResponse($followers);
    }

    /** @test */
    public function it_should_return_generator_with_following_users()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $following = $this->provider->following('username');

        $this->assertIsPaginatedResponse($following);
    }

    /** @test */
    public function it_should_return_generator_with_user_pins()
    {
        $res = [
            'resource'          => [
                'options' => [
                    'bookmarks' => ['my_bookmarks'],
                ],
            ],
            'resource_response' => [
                'data' => [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
        ];
        $this->apiShouldReturn($res);

        $pins = $this->provider->pins('username', 2);
        $this->assertIsPaginatedResponse($pins);
    }

    /** @test */
    public function it_should_return_generator_when_searching()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $this->apiShouldReturn($response);

        $res = $this->provider->search('dogs', 2);
        $this->assertIsPaginatedResponse($res);
    }

    /** @test */
    public function it_should_return_generator_with_user_likes()
    {
        $this->apiShouldReturnPagination()
            ->apiShouldReturnEmpty();

        $likes = $this->provider->likes('username');

        $this->assertIsPaginatedResponse($likes);
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
