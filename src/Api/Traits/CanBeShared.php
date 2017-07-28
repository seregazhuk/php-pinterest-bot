<?php

namespace seregazhuk\PinterestBot\Api\Traits;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;

trait CanBeShared
{
    use HandlesRequest;

    /**
     * @param string $pinId
     * @return bool
     */
    public function shareViaTwitter($pinId)
    {
        return $this->share($pinId, $twitterChannel = 9);
    }

    /**
     * @param string $pinId
     * @return bool
     */
    public function shareViaFacebook($pinId)
    {
        return $this->share($pinId, $facebookChannel = 5);
    }

    /**
     * @param string $pinId
     * @param string $channelId
     * @return bool
     */
    protected function share($pinId, $channelId)
    {
        $request = [
            "invite_type" => [
                "invite_category" => 3,
                "invite_object"   => 1,
                "invite_channel"  => $channelId,
            ],
            "object_id"   => $pinId,
        ];

        return $this->post(UrlBuilder::RESOURCE_SHARE_VIA_SOCIAL, $request);
    }
}
