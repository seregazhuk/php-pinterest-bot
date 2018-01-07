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
        $this->login();
        $provider = $this->getProvider();
        $invites = $provider->invites();

        $this->assertWasGetRequest(
            UrlBuilder::RESOURCE_BOARDS_INVITES, [
                'current_user'  => true,
                'field_set_key' => 'news',
            ]
        );
        $this->assertInternalType('array', $invites);
    }

    /** @test */
    public function it_deletes_invite_for_a_user_and_board()
    {
        $this->login();
        $provider = $this->getProvider();
        $provider->deleteInvite('12345', '56789');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_DELETE_INVITE, [
                'ban'             => false,
                'board_id'        => '12345',
                'field_set_key'   => 'boardEdit',
                'invited_user_id' => '56789',
            ]
        );
    }

    /** @test */
    public function it_allows_a_user_to_ignore_an_invite()
    {
        $this->login();
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['id' => '56789']);

        $provider->ignoreInvite('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_DELETE_INVITE, [
                'board_id'        => '12345',
                'invited_user_id' => '56789',
            ]
        );
    }

    /** @test */
    public function a_user_can_send_an_invitation_to_board_by_id()
    {
        $this->login();
        $provider = $this->getProvider();
        $provider->sendInvite($boardId = '12345', $userId = 5678);

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CREATE_USER_ID_INVITE, [
                'board_id'         => '12345',
                'invited_user_ids' => [5678],
            ]
        );
    }

    /** @test */
    public function a_user_can_send_an_invitation_to_board_by_email()
    {
        $this->login();
        $provider = $this->getProvider();
        $provider->sendInvite($boardId = '12345', $userEmail = 'johndoe@example.com');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CREATE_EMAIL_INVITE, [
                'board_id' => '12345',
                'emails'   => ['johndoe@example.com'],
            ]
        );
    }

    /** @test */
    public function a_user_can_accept_another_users_invite_to_a_board()
    {
        $provider = $this->getProvider();
        $this->login();
        $this->pinterestShouldReturn(['id' => '56789']);
        $provider->acceptInvite('12345');

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_USER_SETTINGS);
        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_ACCEPT_INVITE, [
                'board_id'        => '12345',
                'invited_user_id' => '56789',
            ]
        );
    }

    protected function getProviderClass()
    {
        return Boards::class;
    }
}
