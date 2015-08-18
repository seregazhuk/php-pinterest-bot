<?php

namespace szhuk\tests;

use szhuk\PinterestAPI\ApiRequest;

class PinterestPinnersTest extends PinterestBotTest
{
    public function testGetAccountName()
    {
        $accountName                                                      = 'test';
        $res['resource_data_cache'][1]['resource']['options']['username'] = $accountName;
        $mock                                                             = $this->getMock(ApiRequest::class,
            ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $this->assertEquals($accountName, $this->bot->getAccountName());
        $this->assertNull($this->bot->getAccountName());
    }

    public function testFollowAndUnfollowUser()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn([]);
        $mock->expects($this->at(3))->method('exec')->willReturn([]);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->followUser(1111));
        $this->assertTrue($this->bot->unFollowUser(1111));

        $this->assertFalse($this->bot->followUser(1111));
        $this->assertFalse($this->bot->unFollowUser(1111));

    }


    public function testGetUserData()
    {
        $expected                                = ['data' => ['info' => ''], 'bookmarks' => ['booksmarks_string']];
        $res['resource']['options']['bookmarks'] = $expected['bookmarks'];
        $res['resource_response']['data']        = $expected['data'];

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(3))->method('exec')->willReturn(['resource_response' => []]);

        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $data = $this->bot->getUserData('test_user', 'http://example.com', 'http://example.com');
        $this->assertEquals($expected, $data);

        $data = $this->bot->getUserData('test_user', 'http://example.com', 'http://example.com', 'my_bookmarks');
        $this->assertEquals([], $data);
    }

    public function testGetUserInfo()
    {
        $res['resource_response'] = ['data' => ['name' => 'test']];
        $mock                     = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $data = $this->bot->getUserInfo($this->bot->username);
        $this->assertEquals($res['resource_response']['data'], $data);
    }


    /**
     * Simple response for follow functions
     *
     * @return array
     */
    public function getFollowResponse()
    {
        return [
            'response' => [
                [
                    'resource_response' => [
                        'data' => [
                            ['id' => 1],
                            ['id' => 2],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getFollowResponse
     */
    public function testGetFollowers($response)
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($response);

        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $mock->expects($this->at(3))
            ->method('exec')
            ->willReturn([
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $followers = $this->bot->getFollowers($this->bot->username);
        $this->assertCount(2, iterator_to_array($followers)[0]);

        $followers = $this->bot->getFollowers($this->bot->username);
        $this->assertEmpty(iterator_to_array($followers));

    }

    /**
     * @dataProvider getFollowResponse
     */
    public function testGetFollowing($response)
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($response);

        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $following = $this->bot->getFollowing($this->bot->username);
        $this->assertCount(2, iterator_to_array($following)[0]);
    }

    public function testPinnerPins()
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

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn($res);

        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $pins = $this->bot->getUserPins($this->bot->username, 1);
        $expectedResultsNum = count($res['resource_response']['data']);
        $this->assertCount($expectedResultsNum, iterator_to_array($pins)[0]);
    }

    public function testCheckErrorInResponse()
    {
        $response = [
            [
                'api_error_code' => 404,
                'message'        => 'Not found',
            ],
        ];
        $this->invokeMethod('checkErrorInResponse', $response);
        $this->assertEquals($response[0]['api_error_code'], $this->bot->lastApiErrorCode);
        $this->assertEquals($response[0]['message'], $this->bot->lastApiErrorMsg);

        $this->invokeMethod('checkErrorInResponse', [[]]);
        $this->assertNull($this->bot->lastApiErrorCode);
        $this->assertNull($this->bot->lastApiErrorMsg);
    }
}
