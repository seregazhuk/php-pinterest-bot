<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Interests;

class InterestsTest extends ProviderTest
{
    protected $providerClass = Interests::class;

    public function testFollowAndUnFollow()
    {
        $this->mock->expects($this->at(1))->method('exec')->willReturn([]);
        $this->mock->expects($this->at(3))->method('exec')->willReturn([]);

        $this->setProperty($this->provider, 'request', $this->mock);

        $this->assertTrue($this->provider->follow(1111));
        $this->assertTrue($this->provider->unFollow(1111));

        $this->assertFalse($this->provider->follow(1111));
        $this->assertFalse($this->provider->unFollow(1111));
    }
}