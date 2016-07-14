<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
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
        $this->setFollowSuccessResponse($pinnerId, UrlHelper::RESOURCE_FOLLOW_USER);
        $this->assertTrue($this->provider->follow($pinnerId));

        $this->setFollowErrorResponse($pinnerId, UrlHelper::RESOURCE_FOLLOW_USER);
        $this->assertFalse($this->provider->follow($pinnerId));
    }

    /** @test */
    public function it_should_unfollow_user()
    {
        $pinnerId = 1;
        $this->setFollowSuccessResponse($pinnerId, UrlHelper::RESOURCE_UNFOLLOW_USER);
        $this->assertTrue($this->provider->unFollow($pinnerId));

        $this->setFollowErrorResponse($pinnerId, UrlHelper::RESOURCE_UNFOLLOW_USER);
        $this->assertFalse($this->provider->unFollow($pinnerId));
    }

    /** @test */
    public function it_should_return_user_info()
    {
        $response = $this->createApiResponse(['data' => ['name' => 'test']]);
        $this->setResponse($response);

        $data = $this->provider->info('username');
        $this->assertEquals($response['resource_response']['data'], $data);
    }

    /** @test */
    public function it_should_return_iterator_with_user_followers()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResourceResponseData([]);
        $this->setResourceResponseData([['type' => 'module']]);

        $followers = $this->provider->followers('username');
        $this->assertCount(2, iterator_to_array($followers));

        $followers = $this->provider->followers('username');
        $this->assertEmpty(iterator_to_array($followers));
    }

    /** @test */
    public function it_should_return_iterator_with_following_users()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResourceResponseData([]);

        $following = $this->provider->following('username');
        $this->assertCount(2, iterator_to_array($following));
    }

    /** @test */
    public function it_should_return_iterator_with_user_pins()
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
        $this->setResponse($res);

        $pins = $this->provider->pins('username', 2);
        $expectedResultsNum = count($res['resource_response']['data']);
        $this->assertCount($expectedResultsNum, iterator_to_array($pins));
    }

    /** @test */
    public function it_should_return_iterator_when_searching()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->setResponse($response, 2);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res);
    }
}
