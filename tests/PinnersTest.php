<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Pinners;

class PinnersTest extends ProviderTest
{
    protected $providerClass = Pinners::class;

    public function testFollow()
    {
        $response = ['body' => 'result'];
        $mock     = $this->createRequestMock();
        $mock->method('exec')->willReturn(json_encode($response));
        $this->setProperty($this->provider, 'request', $mock);
        $this->assertTrue($this->provider->follow(1));
        $this->assertTrue($this->provider->unFollow(1));
    }

    public function testGetUserData()
    {
        $expected                                = ['data' => ['info' => ''], 'bookmarks' => ['booksmarks_string']];
        $res['resource']['options']['bookmarks'] = $expected['bookmarks'];
        $res['resource_response']['data']        = $expected['data'];

        $mock = $this->createRequestMock();
        $mock->method('exec')->willReturn($res);
        $this->setProperty($this->provider, 'request', $mock);

        $data = $this->provider->getUserData('test_user', 'request://example.com', 'request://example.com');
        $this->assertEquals($expected, $data);
    }

    public function testInfo()
    {
        $res['resource_response'] = ['data' => ['name' => 'test']];
        $mock = $this->createRequestMock();
        $mock->method('exec')->willReturn($res);
        $this->setProperty($this->provider, 'request', $mock);
        $data = $this->provider->info('username');
        $this->assertEquals($res['resource_response']['data'], $data);
    }

    public function testMyAccountName()
    {
        $accountName                                                      = 'test';
        $res['resource_data_cache'][1]['resource']['options']['username'] = $accountName;
        $mock                                                             = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $this->setProperty($this->provider, 'request', $mock);
        $this->assertEquals($accountName, $this->provider->myAccountName());
        $this->assertNull($this->provider->myAccountName());
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
     * @param $response
     */
    public function testGetFollowers($response)
    {
        $mock = $this->createRequestMock();
        $mock->expects($this->at(0))
            ->method('exec')
            ->willReturn($response);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);
        $mock->expects($this->at(2))
            ->method('exec')
            ->willReturn([
                'resource_response' => [
                    'data' => [
                        ['type' => 'module'],
                    ],
                ],
            ]);
        $this->setProperty($this->provider, 'request', $mock);
        $followers = $this->provider->followers('username');
        $this->assertCount(2, iterator_to_array($followers)[0]);
        $followers = $this->provider->followers('username');
        $this->assertEmpty(iterator_to_array($followers));
    }

    /**
     * @dataProvider getFollowResponse
     * @param array $response
     */
    public function testGetFollowing($response)
    {
        $mock = $this->createRequestMock();
        $mock->expects($this->at(0))
            ->method('exec')
            ->willReturn($response);
        $mock->expects($this->at(1))
            ->method('exec')
            ->willReturn(['resource_response' => ['data' => []]]);

        $this->setProperty($this->provider, 'request', $mock);
        $following = $this->provider->following('username');
        $this->assertCount(2, iterator_to_array($following)[0]);
    }

    public function testPins()
    {
        $res  = [
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
        $mock = $this->createRequestMock();
        $mock->expects($this->at(0))
            ->method('exec')
            ->willReturn($res);
        $this->setProperty($this->provider, 'request', $mock);
        $pins = $this->provider->pins('username', 1);
        $expectedResultsNum = count($res['resource_response']['data']);
        $this->assertCount($expectedResultsNum, iterator_to_array($pins)[0]);
    }

    public function testSearch()
    {

        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $mock               = $this->createRequestMock();

        $mock->method('exec')->willReturn($response);
        $this->setProperty($this->provider, 'request', $mock);
        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }
}
