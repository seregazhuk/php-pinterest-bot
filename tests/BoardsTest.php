<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Boards;

class BoardsTest extends ProviderTest
{

    /**
     * @var Boards
     */
    protected $providerClass = Boards::class;

    public function testSearch()
    {
        $response['module']['tree']['data']['results'] = [
            ['id' => 1],
            ['id' => 2],
        ];

        $expectedResultsNum = count($response['module']['tree']['data']['results']);
        $this->mock->method('exec')->willReturn($response);
        $this->setProperty($this->provider, 'request', $this->mock);
        $res = iterator_to_array($this->provider->search('dogs'), 1);
        $this->assertCount($expectedResultsNum, $res[0]);
    }

    public function testFollowAndUnfollow()
    {
        $this->mock->expects($this->at(1))->method('exec')->willReturn([]);
        $this->mock->expects($this->at(3))->method('exec')->willReturn([]);
        $this->setProperty($this->provider, 'request', $this->mock);
        $this->assertTrue($this->provider->follow(1111));
        $this->assertTrue($this->provider->unFollow(1111));
        $this->assertFalse($this->provider->follow(1111));
        $this->assertFalse($this->provider->unFollow(1111));
    }

    public function testMy()
    {
        $initBoards                                     = ['first', 'second'];
        $res['resource_response']['data']['all_boards'] = $initBoards;
        $this->mock->method('exec')->willReturn($res);
        $this->setProperty($this->provider, 'request', $this->mock);
        $boards = $this->provider->my();
        $this->assertEquals($initBoards, $boards);
        $res = null;

        $this->mock = $this->createRequestMock();
        $this->mock->method('exec')->willReturn(json_encode($res));
        $this->setProperty($this->provider, 'request', $this->mock);
        $boards = $this->provider->my();
        $this->assertFalse($boards);
    }

}