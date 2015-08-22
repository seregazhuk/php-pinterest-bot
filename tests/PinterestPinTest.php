<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\ApiRequest;

class PinterestPinTest extends PinterestBotTest
{

    public function testLikeAndUnlikePin()
    {
        $res['resource_response'] = [];
        $mock                     = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(3))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->likePin(1111));
        $this->assertTrue($this->bot->unLikePin(1111));

        $this->assertFalse($this->bot->likePin(1111));
        $this->assertFalse($this->bot->unLikePin(1111));
    }

    public function testCommentPin()
    {
        $res['resource_response'] = [];
        $mock                     = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->commentPin(1111, 'comment text'));
        $this->assertFalse($this->bot->commentPin(1111, 'comment text'));
    }

    public function testPin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $pinSource      = 'http://example.com/image.jpg';
        $pinDescription = 'Pin Description';
        $boardId        = 1;
        $this->assertNotFalse($this->bot->pin($pinSource, $boardId, $pinDescription));
        $this->assertFalse($this->bot->pin($pinSource, $boardId, $pinDescription));
    }

    public function testRepin()
    {
        $res['resource_response']['data']['id'] = 1;
        $mock                                   = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $repinId        = 11;
        $pinDescription = 'Pin Description';
        $boardId        = 1;
        $this->assertNotFalse($this->bot->repin($repinId, $boardId, $pinDescription));
        $this->assertFalse($this->bot->repin($repinId, $boardId, $pinDescription));
    }

    public function testDeletePin()
    {
        $res['resource_response']['data'] = [];
        $mock                             = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $this->assertNotFalse($this->bot->deletePin(1));
        $this->assertFalse($this->bot->deletePin(1));
    }

    public function testGetPinInfo()
    {
        $res['resource_response']['data'] = ['data'];
        $mock                             = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(0))->method('exec')->willReturn($res);
        $mock->expects($this->at(1))->method('exec')->willReturn(['resource_response' => []]);
        $mock->method('isLoggedIn')->willReturn(true);
        $this->setProperty('api', $mock);

        $this->assertNotNull($this->bot->getPinInfo(1));
        $this->assertNull($this->bot->getPinInfo(1));
    }

}
