<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\Pagination;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;
use seregazhuk\PinterestBot\Api\ProvidersContainer;

/**
 * @property ProvidersContainer container
 */
trait BoardInvites
{
    use HandlesRequest;

    /**
     * @return string[]
     */
    protected function requiresLoginForBoardInvites()
    {
        return [
            'sendInvite',
            'sendInviteByEmail',
            'sendInviteByUserId',
            'ignoreInvite',
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

        $invites = $this->get(UrlBuilder::RESOURCE_BOARDS_INVITES, $data);

        return !$invites ? [] : $invites;
    }

    /**
     * Get invites for a specified board
     *
     * @param string $boardId
     * @param int $limit
     * @return Pagination
     */
    public function invitesFor($boardId, $limit = Pagination::DEFAULT_LIMIT)
    {
        return $this->paginate(
            UrlBuilder::RESOURCE_BOARDS_INVITES, ['board_id' => (string) $boardId], $limit
        );
    }

    /**
     * @param string $boardId
     * @param string|array $emails
     * @return bool
     */
    public function sendInviteByEmail($boardId, $emails)
    {
        $emails = (array)$emails;
        $data = [
            'board_id' => $boardId,
            'emails'   => $emails,
        ];

        return $this->post(UrlBuilder::RESOURCE_CREATE_EMAIL_INVITE, $data);
    }

    /**
     * @param string $boardId
     * @param string|array $users
     * @return bool
     */
    public function sendInvite($boardId, $users)
    {
        $users = (array)$users;

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
        $data = [
            'board_id'         => $boardId,
            'invited_user_ids' => (array)$userIds,
        ];

        return $this->post(UrlBuilder::RESOURCE_CREATE_USER_ID_INVITE, $data);
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

        return $this->post(UrlBuilder::RESOURCE_DELETE_INVITE, $data);
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

        return $this->post($endpoint, $data);
    }
}
