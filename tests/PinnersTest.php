<?php

namespace seregazhuk\tests;

use LogicException;
use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\tests\Helpers\FollowResponseHelper;
use seregazhuk\PinterestBot\Api\Providers\Pinners;
use seregazhuk\PinterestBot\Exceptions\AuthException;

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
     * @var
     */
    protected $providerClass = Pinners::class;

    /** @test */
    public function followUser()
    {
        $pinnerId = 1;
        $this->setFollowSuccessResponse($pinnerId, UrlHelper::RESOURCE_FOLLOW_USER);
        $this->assertTrue($this->provider->follow($pinnerId));

        $this->setFollowErrorResponse($pinnerId, UrlHelper::RESOURCE_FOLLOW_USER);
        $this->assertFalse($this->provider->follow($pinnerId));
    }

    /** @test */
    public function unFollowUser()
    {
        $pinnerId = 1;
        $this->setFollowSuccessResponse($pinnerId, UrlHelper::RESOURCE_UNFOLLOW_USER);
        $this->assertTrue($this->provider->unFollow(1));

        $this->setFollowErrorResponse($pinnerId, UrlHelper::RESOURCE_UNFOLLOW_USER);
        $this->assertFalse($this->provider->unFollow(1));
    }

    /** @test */
    public function getUserInfo()
    {
        $response = $this->createApiResponse(['data' => ['name' => 'test']]);
        $this->setResponse($response);

        $data = $this->provider->info('username');
        $this->assertEquals($response['resource_response']['data'], $data);
    }

    /** @test */
    public function getUserFollowers()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResponse(['resource_response' => ['data' => []]]);
        $this->setResponse([
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]
        );

        $followers = $this->provider->followers('username');
        $this->assertCount(2, iterator_to_array($followers));

        $followers = $this->provider->followers('username');
        $this->assertEmpty(iterator_to_array($followers));
    }

    /** @test */
    public function getFollowingUsers()
    {
        $response = $this->createPaginatedResponse();
        $this->setResponse($response);
        $this->setResponse(['resource_response' => ['data' => []]]);

        $following = $this->provider->following('username');
        $this->assertCount(2, iterator_to_array($following));
    }

    /** @test */
    public function getUserPins()
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
    public function searchForUsers()
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

    /** @test */
    public function loginWithEmptyCredentials()
    {
        $this->expectException(LogicException::class);
        $this->requestMock->shouldReceive('isLoggedIn')->once()->andReturn(false);
        $this->provider->login('', '');
    }

    /** @test */
    public function loginWhenAlreadyLogged()
    {
        $this->requestMock->shouldReceive('isLoggedIn')->once()->andReturn(true);
        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function successLogin()
    {
        $response = $this->createSuccessApiResponse();
        $this->requestMock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->requestMock->shouldReceive('exec')->andReturn($response);
        $this->requestMock->shouldReceive('clearToken')->once();
        $this->requestMock->shouldReceive('login')->once();

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function loginFails()
    {
        $this->expectException(AuthException::class);

        $response = $this->createErrorApiResponse();
        $this->requestMock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->requestMock->shouldReceive('exec')->andReturn($response);
        $this->requestMock->shouldReceive('clearToken');

        $this->provider->login('test', 'test');
    }

    /** @test */
    public function logout()
    {
        $this->requestMock->shouldReceive('logout');
        $this->provider->logout();
    }
}
