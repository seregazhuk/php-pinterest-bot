<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Comments;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class CommentsTest
 *
 * @method Comments getProvider()
 */
class CommentsBaseTest extends ProviderBaseTest
{
    /** @test */
    public function it_create_a_comment_for_a_pin()
    {
        $provider = $this->getProvider();
        $provider->create('123456', 'comment text');

        $request = [
            'pin_id' => '123456',
            'text' => 'comment text',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_COMMENT_PIN, $request);
    }

    /** @test */
    public function it_deletes_a_comment_by_pin_and_id()
    {
        $provider = $this->getProvider();
        $provider->delete('123456', '111111');

        $request = [
            'pin_id' => '123456',
            'comment_id' => '111111',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_COMMENT_DELETE_PIN, $request);
    }

    protected function getProviderClass()
    {
        return Comments::class;
    }
}
