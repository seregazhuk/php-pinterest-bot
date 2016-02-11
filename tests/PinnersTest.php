<?php

namespace seregazhuk\tests;

use LogicException;
use seregazhuk\PinterestBot\Api\Providers\Pinners;

class PinnersTest extends ProviderTest
{
    /**
     * @var Pinners
     */
    protected $provider;
    protected $providerClass = Pinners::class;

    /** @test */
    public function followUser()
    {
        $response = $this->createSuccessApiResponse();
        $this->mock->shouldReceive('followMethodCall')->andReturn($response);

        $this->assertTrue($this->provider->follow(1));
        $this->assertTrue($this->provider->follow(1));
    }

    /** @test */
    public function unFollowUser()
    {
        $response = $this->createSuccessApiResponse();
        $this->mock->shouldReceive('followMethodCall')->andReturn($response);

        $this->assertTrue($this->provider->unfollow(1));
        $this->assertTrue($this->provider->unfollow(1));
    }

    /** @test */
    public function getUserInfo()
    {
        $response = $this->createApiResponse(['data' => ['name' => 'test']]);
        $this->mock->shouldReceive('exec')->andReturn($response);

        $data = $this->provider->info('username');
        $this->assertEquals($response['resource_response']['data'], $data);
    }

    /** @test */
    public function getUserFollowers()
    {
        $response = $this->createPaginatedResponse();
        $this->mock->shouldReceive('exec')->once()->andReturn($response);

        $this->mock->shouldReceive('exec')->once()->andReturn(['resource_response' => ['data' => []]]);

        $this->mock->shouldReceive('exec')->once()->andReturn(
            [
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
        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturn(['resource_response' => ['data' => []]]);

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
        $this->mock->shouldReceive('exec')->once()->andReturn($res);

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
        $this->mock->shouldReceive('exec')->twice()->andReturn($response);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    /**
     * @test
     * @expectedException LogicException
     */
    public function loginWithEmptyCredentials()
    {
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

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\AuthException
     */
    public function loginFails()
    {
        $response = $this->createErrorApiResponse();
        $this->mock->shouldReceive('isLoggedIn')->andReturn(false);
        $this->mock->shouldReceive('exec')->andReturn($response);
        $this->mock->shouldReceive('clearToken');

        $this->provider->login('test', 'test');
    }
}
