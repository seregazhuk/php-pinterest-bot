<?php

namespace szhuk\tests;

use szhuk\PinterestAPI\ApiRequest;

class PinterestBoardsTest extends PinterestBotTest
{

    public function testGetBoards()
    {
        $initBoards                                     = ['first', 'second'];
        $res['resource_response']['data']['all_boards'] = $initBoards;

        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);
        $boards = $this->bot->getBoards();
        $this->assertEquals($initBoards, $boards);

        $res  = null;
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);
        $boards = $this->bot->getBoards();
        $this->assertNull($boards);
    }


    public function testFollowAndUnfollowBoards()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn([]);
        $mock->expects($this->at(3))->method('exec')->willReturn([]);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->followBoard(1111));
        $this->assertTrue($this->bot->unFollowBoard(1111));

        $this->assertFalse($this->bot->followBoard(1111));
        $this->assertFalse($this->bot->unFollowBoard(1111));

    }
}
