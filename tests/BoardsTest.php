<?php

namespace seregazhuk\tests;


use seregazhuk\PinterestBot\Providers\Boards;


class BoardsTest extends ProviderTest
{

    /**
     * @var Boards
     */
    protected $provider;


    protected function setUp()
    {
        $this->provider = new Boards($this->createRequestMock());
        parent::setUp();
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
        $this->setProperty('request', $mock);
        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    public function testFollowAndUnfollow()
    {
        $mock = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn([]);
        $mock->expects($this->at(3))->method('exec')->willReturn([]);
        $this->setProperty('request', $mock);
        $this->assertTrue($this->provider->follow(1111));
        $this->assertTrue($this->provider->unFollow(1111));
        $this->assertFalse($this->provider->follow(1111));
        $this->assertFalse($this->provider->unFollow(1111));
    }

    public function testMy()
    {
        $initBoards                                     = ['first', 'second'];
        $res['resource_response']['data']['all_boards'] = $initBoards;
        $mock                                           = $this->createRequestMock();
        $mock->method('exec')->willReturn($res);
        $this->setProperty('request', $mock);
        $boards = $this->provider->my();
        $this->assertEquals($initBoards, $boards);
        $res = null;

        $mock = $this->createRequestMock();
        $mock->method('exec')->willReturn(json_encode($res));
        $this->setProperty('request', $mock);
        $boards = $this->provider->my();
        $this->assertNull($boards);
    }

}