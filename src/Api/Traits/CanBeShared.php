<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait CanBeShared
{
    use HandlesRequest, ResolvesCurrentUser;

    /**
     * @return array
     */
    protected function requiresLoginForCanBeShared()
    {
        return [
            'share',
            'markAsGood',
            'markAsBad',
        ];
    }

    /**
     * @param string $pinId
     * @return string
     */
    public function share($pinId)
    {
        $request = [
            "invite_type" => [
                "invite_category" => 3, // magic numbers, but I have
                "invite_object"   => 1, // no idea what do they mean
                "invite_channel"  => $linkChannel = 12,
            ],
            "object_id"   => $pinId,
        ];

        $response = $this->post(UrlBuilder::RESOURCE_SHARE_VIA_SOCIAL, $request, true);

        return isset($response['invite_url']) ? $response['invite_url'] : '';
    }

    /**
     * @param string $pinId
     * @param $userId
     * @return array|bool
     */
    public function leaveGoodReaction($pinId, $userId)
    {
        return $this->reactOnPinInConversation($pinId, $userId, "👍");
    }

    /**
     * @param string $pinId
     * @param $userId
     * @return array|bool
     */
    public function leaveBadReaction($pinId, $userId)
    {
        return $this->reactOnPinInConversation($pinId, $userId, "👎");
    }

    /**
     * @param string $pinId
     * @param string $userId
     * @param string $reaction
     * @return array|bool
     */
    protected function reactOnPinInConversation($pinId, $userId, $reaction)
    {
        $request = [
            "user_ids" => [$userId],
            "pin"      => (string)$pinId,
            "text"     => $reaction,
        ];

        return $this->post(UrlBuilder::RESOURCE_SEND_MESSAGE, $request);
    }

    public function react($link)
    {
        var_dump($this->getHtml($link)); die('a');
    }
}
