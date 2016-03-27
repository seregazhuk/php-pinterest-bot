<?php

namespace seregazhuk\tests;

use LogicException;
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
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->follow(1));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->follow(1));
    }

    /** @test */
    public function unFollowUser()
    {
        $this->setFollowSuccessResponse();
        $this->assertTrue($this->provider->unfollow(1));

        $this->setFollowErrorResponse();
        $this->assertFalse($this->provider->unfollow(1));
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
        $this->assertCount(2, iterator_to_array($followers)[0]);

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
        $this->assertCount(2, iterator_to_array($following)[0]);
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

        $pins = $this->provider->pins('username', 1);
        $expectedResultsNum = count($res['resource_response']['data']);
        $this->assertCount($expectedResultsNum, iterator_to_array($pins)[0]);
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
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    /** @test */
    public function loginWithEmptyCredentials()
    {
        $this->expectException(LogicException::class);
        $this->mock->shouldReceive('isLoggedIn')->once()->andReturn(false);
        $this->provider->login('', '');
    }

    /** @test */
    public function loginWhenAlreadyLogged()
    {
        $this->mock->shouldReceive('isLoggedIn')->once()->andReturn(true);
        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function successLogin()
    {
        $response = $this->createSuccessApiResponse();
        $this->mock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->mock->shouldReceive('exec')->andReturn($response);
        $this->mock->shouldReceive('clearToken')->once();
        $this->mock->shouldReceive('setLoggedIn')->once();

        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function loginFails()
    {
        $this->expectException(AuthException::class);

        $response = $this->createErrorApiResponse();
        $this->mock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->mock->shouldReceive('exec')->andReturn($response);
        $this->mock->shouldReceive('clearToken');

        $this->provider->login('test', 'test');
    }
}
