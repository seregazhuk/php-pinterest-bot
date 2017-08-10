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
    public function a_user_can_share_a_pin()
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

    protected function getProviderClass()
    {
        return Pins::class;
    }
}
