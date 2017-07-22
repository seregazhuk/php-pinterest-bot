<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait BoardInvites
{
    use HandlesRequest;

    /**
     * @return array
     */
    protected function requiresLoginForBoardInvites()
    {
        return [
            'sendInvite',
            'sendInviteByEmail',
            'sendInviteByUserId',
            'deleteInvite',
            'acceptInvite',
            'invites',
        ];
    }

    /**
     * Get boards invites
     * @return array
     */
    public function invites()
    {
        $data = [
            'current_user'  => true,
            'field_set_key' => 'news',
        ];

        $invites = $this->get($data, UrlBuilder::RESOURCE_BOARDS_INVITES);

        return !$invites ? [] : $invites;
    }

    /**
     * @param string $boardId
     * @param string|array $emails
     * @return bool
     */
    public function sendInviteByEmail($boardId, $emails)
    {
        $emails = is_array($emails) ? $emails : [$emails];
        $data = [
            "board_id" => $boardId,
            "emails"   => $emails,
        ];

        return $this->post($data, UrlBuilder::RESOURCE_CREATE_EMAIL_INVITE);
    }

    /**
     * @param string $boardId
     * @param string|array $users
     * @return bool
     */
    public function sendInvite($boardId, $users)
    {
        $users = is_array($users) ? $users : [$users];

        $isEmail = filter_var($users[0], FILTER_VALIDATE_EMAIL);

        return $isEmail ?
            $this->sendInviteByEmail($boardId, $users) :
            $this->sendInviteByUserId($boardId, $users);
    }

    /**
     * @param string $boardId
     * @param string|array $userIds
     * @return bool
     */
    public function sendInviteByUserId($boardId, $userIds)
    {
        $userIds = is_array($userIds) ? $userIds : [$userIds];
        $data = [
            "board_id"         => $boardId,
            "invited_user_ids" => $userIds,
        ];

        return $this->post($data, UrlBuilder::RESOURCE_CREATE_USER_ID_INVITE);
    }

    /**
     * @param string $boardId
     * @param string $userId
     * @param bool $ban
     * @return bool
     */
    public function deleteInvite($boardId, $userId, $ban = false)
    {
        $data = [
            'ban'             => $ban,
            'board_id'        => $boardId,
            'field_set_key'   => 'boardEdit',
            'invited_user_id' => $userId,
        ];

        return $this->post($data, UrlBuilder::RESOURCE_DELETE_INVITE);
    }

    /**
     * @param string $boardId
     * @return bool
     */
    public function ignoreInvite($boardId)
    {
        return $this->makeInviteCall($boardId, UrlBuilder::RESOURCE_DELETE_INVITE);
    }

    /**
     * @param string $boardId
     * @return bool
     */
    public function acceptInvite($boardId)
    {
        return $this->makeInviteCall($boardId, UrlBuilder::RESOURCE_ACCEPT_INVITE);
    }

    /**
     * @param string $boardId
     * @param string $endpoint
     * @return bool
     */
    protected function makeInviteCall($boardId, $endpoint)
    {
        $data = [
            'board_id'        => $boardId,
            'invited_user_id' => $this->container->user->id(),
        ];

        return $this->post($data, $endpoint);
    }
}