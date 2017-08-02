<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class PinsTest
 * @method Pins getProvider()
 */
class PinsTest extends ProviderBaseTest
{
    /** @test */
    public function a_user_can_like_a_pin()
    {
        $provider = $this->getProvider();
        $provider->like('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_LIKE_PIN, ['pin_id' => '12345']);
    }

    /** @test */
    public function a_user_can_dislike_a_pin()
    {
        $provider = $this->getProvider();
        $provider->unLike('12345');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_UNLIKE_PIN, ['pin_id' => '12345']);
    }

    /** @test */
    public function it_retrieves_detailed_info_for_a_pin()
    {
        $provider = $this->getProvider();
        $provider->info('12345');

        $request = [
            'id' => '12345',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, $request);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Pins::class;
    }
}
