<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Interests;

class InterestsTest extends ProviderTest
{
    /**
     * @var Interests
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Interests($this->createRequestMock());
        parent::setUp();
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
}