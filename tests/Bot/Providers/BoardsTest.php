<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class BoardsTest
 * @method Boards getProvider()
 */
class BoardsTest extends BaseProviderTest
{
    /** @test */
    public function it_fetches_boards_for_a_specified_user()
    {
        $provider = $this->getProvider();

        $provider->forUser('johnDoe');

        $request = [
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARDS, $request);
    }

    /** @test */
    public function it_fetches_boards_for_current_user()
    {
    }

    protected function getProviderClass()
    {
        return Boards::class;
    }
}