<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\ApiRequest;

class InterestTest extends BotTest
{
    public function testFollowAndUnfollowInterest()
    {
        $mock = $this->getMock(ApiRequest::class, ['exec', 'isLoggedIn']);
        $mock->expects($this->at(1))->method('exec')->willReturn([]);
        $mock->expects($this->at(3))->method('exec')->willReturn([]);
        $mock->method('isLoggedIn')->willReturn(true);

        $this->setProperty('api', $mock);

        $this->assertTrue($this->bot->followInterest(1111));
        $this->assertTrue($this->bot->unFollowInterest(1111));

        $this->assertFalse($this->bot->followInterest(1111));
        $this->assertFalse($this->bot->unFollowInterest(1111));

    }
}