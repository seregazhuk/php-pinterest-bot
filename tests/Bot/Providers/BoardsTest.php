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
        $provider = $this->getProvider();
        $this->login();
        $this->setResponse(['username' => 'johnDoe']);

        $provider->forMe();


        // Request to receive user settings
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);

        // Makes request for the retrieved username from the profile
        $request = [
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARDS, $request);
    }

    /** @test */
    public function it_fetches_info_for_a_specified_board()
    {
        $provider = $this->getProvider();

        $provider->info('johnDoe', 'my-board-name');

        $request = [
            'slug'          => 'my-board-name',
            'username'      => 'johnDoe',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_BOARD, $request);
    }

    protected function getProviderClass()
    {
        return Boards::class;
    }
}