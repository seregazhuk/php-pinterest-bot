<?php

namespace seregazhuk\tests;

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
        $this->mock->method('exec')->willReturn($response);

        $this->assertTrue($this->provider->follow(1));
        $this->assertTrue($this->provider->follow(1));
    }

    /** @test */
    public function unFollowUser()
    {
        $response = $this->createSuccessApiResponse();
        $this->mock->method('exec')->willReturn($response);

        $this->assertTrue($this->provider->unfollow(1));
        $this->assertTrue($this->provider->unfollow(1));
    }

    /** @test */
    public function getUserData()
    {
        $expected = ['data' => ['info' => ''], 'bookmarks' => ['booksmarks_string']];
        $res['resource']['options']['bookmarks'] = $expected['bookmarks'];
        $res['resource_response']['data'] = $expected['data'];
        $this->mock->method('exec')->willReturn($res);

        $data = $this->provider->getUserData('test_user', 'request://example.com', 'request://example.com');
        $this->assertEquals($expected, $data);
    }

    /** @test */
    public function getUserInfo()
    {
        $response = $this->createApiResponse(['data' => ['name' => 'test']]);
        $this->mock->method('exec')->willReturn($response);

        $data = $this->provider->info('username');
        $this->assertEquals($response['resource_response']['data'], $data);
    }

    /** @test */
    public function getUserFollowers()
    {
        $response = $this->createPaginatedResponse();
        $this->mock->expects($this->at(0))->method('exec')->willReturn($response);

        $this->mock->expects($this->at(1))->method('exec')->willReturn(['resource_response' => ['data' => []]]);

        $this->mock->expects($this->at(2))->method('exec')->willReturn(
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
        $this->mock->expects($this->at(0))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(1))->method('exec')->willReturn(['resource_response' => ['data' => []]]);

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
        $this->mock->expects($this->at(0))->method('exec')->willReturn($res);

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
        $this->mock->method('exec')->willReturn($response);

        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    /** @test */
    public function successLogin()
    {
        $response = $this->createSuccessApiResponse();
        $this->mock->method('isLoggedIn')->willReturn(false);
        $this->mock->method('exec')->willReturn($response);
        $this->assertTrue($this->provider->login('test', 'test'));
    }

    /** @test */
    public function loginFails()
    {
        $response = $this->createErrorApiResponse();
        $this->mock->method('isLoggedIn')->willReturn(false);
        $this->mock->method('exec')->willReturn($response);
        $this->assertFalse($this->provider->login('test', 'test'));
    }
}
