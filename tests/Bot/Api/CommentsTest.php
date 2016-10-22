<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\Providers\Comments;

/**
 * Class CommentsTest.
 */
class CommentsTest extends ProviderTest
{
    /**
     * @var Comments
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Comments::class;


    /** @test */
    public function it_should_create_comments_for_pin()
    {
        $this->apiShouldReturnSuccess()
            ->assertNotEmpty($this->provider->create(1111, 'comment text'));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->create(1111, 'comment text'));
    }

    /** @test */
    public function it_should_delete_comments_for_pin()
    {
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->delete(1111, 1111));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->delete(1111, 1111));
    }
}
