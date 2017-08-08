<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Boards;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class BoardInvitesTest
 * @method Boards getProvider()
 */
class BoardInvitesTest extends ProviderBaseTest
{
    /** @test */
    public function it_returns_invites_for_a_current_user()
    {
        $provider = $this->getProvider();
        $invites = $provider->invites();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_BOARDS_INVITES, [
            'current_user'  => true,
            'field_set_key' => 'news',
        ]);
        $this->assertInternalType('array', $invites);
    }

    /** @test */
    public function it_deletes_invite_for_a_user_and_board()
    {
        $provider = $this->getProvider();
        $provider->deleteInvite('12345', '56789');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DELETE_INVITE, [
            'ban'             => false,
            'board_id'        => '12345',
            'field_set_key'   => 'boardEdit',
            'invited_user_id' => '56789',
        ]);
    }

    /** @test */
    public function it_allows_a_user_to_ignore_an_invite()
    {
        $provider = $this->getProvider();

        // For resolving current user id
        $this->login();
        $this->pinterestShouldReturn(['id' => '56789']);

        $provider->ignoreInvite('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_DELETE_INVITE, [
            'board_id'        => '12345',
            'invited_user_id' => '56789',
        ]);
    }

    protected function getProviderClass()
    {
        return Boards::class;
    }
}
