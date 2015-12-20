<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Interests;

/**
 * Class InterestsTest
 * @package seregazhuk\tests
 */
class InterestsTest extends ProviderTest
{
    /**
     * @var Interests
     */
    protected $provider;
    protected $providerClass = Interests::class;

    public function testFollowAndUnFollow()
    {
        $request = $this->createSuccessApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($request);
        $this->mock->expects($this->at(3))->method('exec')->willReturn(null);

        $this->assertTrue($this->provider->follow(1111));
        $this->assertFalse($this->provider->follow(1111));
    }

    public function testUnFollow()
    {
        $request = $this->createApiResponse(['data' =>'success']);

        $this->mock->expects($this->at(1))->method('exec')->willReturn($request);
        $this->mock->expects($this->at(3))->method('exec')->willReturn(null);

        $this->assertTrue($this->provider->unFollow(1111));
        $this->assertFalse($this->provider->unFollow(1111));
    }
}