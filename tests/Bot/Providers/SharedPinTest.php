<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class SharedPinTest
 * @method Pins getProvider()
 */
class SharedPinTest extends ProviderBaseTest
{
    /** @test */
    public function a_user_can_share_a_pin_via_link()
    {
        $provider = $this->getProvider();
        $provider->share('12345');

        $request = [
            "invite_type" => [
                "invite_category" => 3, // magic numbers, but I have
                "invite_object"   => 1, // no idea what do they mean
                "invite_channel"  => $linkChannel = 12,
            ],
            "object_id"   => '12345',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_SHARE_VIA_SOCIAL, $request);
    }

    /** @test */
    public function a_user_can_share_a_pin_via_twitter()
    {
        $provider = $this->getProvider();
        $provider->shareViaTwitter('12345');

        $request = [
            "invite_type" => [
                "invite_category" => 3, // magic numbers, but I have
                "invite_object"   => 1, // no idea what do they mean
                "invite_channel"  => $twitterChannel = 9,
            ],
            "object_id"   => '12345',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_SHARE_VIA_SOCIAL, $request);
    }

    /** @test */
    public function a_user_can_leave_good_reaction_on_pin()
    {
        $provider = $this->getProvider();
        $provider->leaveGoodReaction('12345', $userId = 6789);

        $this->assertWasReactionRequest('12345', 6789, "👍");
    }

    /** @test */
    public function a_user_can_leave_bad_reaction_on_pin()
    {
        $provider = $this->getProvider();
        $provider->leaveBadReaction('12345', $userId = 6789);

        $this->assertWasReactionRequest('12345', 6789, "👎");
    }

    protected function getProviderClass()
    {
        return Pins::class;
    }

    /**
     * @param int $pinId
     * @param int $userId
     * @param string $reaction
     */
    private function assertWasReactionRequest($pinId, $userId, $reaction)
    {
        $request = [
            "user_ids" => [$userId],
            "pin"      => $pinId,
            "text"     => $reaction,
        ];

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_SEND_MESSAGE, $request);
    }
}
