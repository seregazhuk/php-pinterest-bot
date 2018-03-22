<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Comments;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class CommentsTest
 *
 * @method Comments getProvider()
 */
class CommentsTest extends ProviderBaseTest
{
    /** @test */
    public function it_fetches_aggregated_pin_id_to_create_a_comment()
    {
        $provider = $this->getProvider();
        $provider->create('123456', 'comment text');

        // To resolve pin aggregated id
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, [
                'id'            => '123456',
                'field_set_key' => 'detailed',
            ]
        );
    }

    /** @test */
    public function it_fetches_aggregated_pin_id_to_get_list_of_comments()
    {
        $provider = $this->getProvider();
        $provider->getList('123456');

        // To resolve pin aggregated id
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, [
                'id'            => '123456',
                'field_set_key' => 'detailed',
            ]
        );
    }

    /** @test */
    public function it_deletes_a_comment_by_pin_and_id()
    {
        $provider = $this->getProvider();
        $provider->delete('123456', '111111');

        $request = ['commentId' => '111111'];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_COMMENT_DELETE_PIN, $request);
    }

    protected function getProviderClass()
    {
        return Comments::class;
    }
}
