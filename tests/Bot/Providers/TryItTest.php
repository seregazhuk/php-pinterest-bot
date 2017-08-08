<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Pins;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class TryItTest
 * @method Pins getProvider()
 */
class TryItTest extends ProviderBaseTest
{
    /** @test */
    public function a_user_can_try_a_pin()
    {
        $provider = $this->getProvider();
        $provider->tryIt('12345', 'my comment');

        $request = [
            'pin_id'  => '12345',
            'details' => 'my comment',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_TRY_PIN_CREATE, $request);
    }

    /** @test */
    public function a_user_can_edit_try_it_of_the_pin()
    {
        $provider = $this->getProvider();
        $provider->editTryIt('12345', '56789', 'my comment');

        $request = [
            'pin_id'              => '12345',
            'details'             => 'my comment',
            'user_did_it_data_id' => '56789',
        ];
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_TRY_PIN_EDIT, $request);
    }

    /** @test */
    public function a_user_can_delete_a_try_it_record()
    {
        $provider = $this->getProvider();
        $provider->deleteTryIt('1234567');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_TRY_PIN_DELETE,
            ['user_did_it_data_id' => '1234567']
        );
    }

    /** @test */
    public function it_can_fetch_users_who_have_tried_a_pin()
    {
        $provider = $this->getProvider();
        $this->pinterestShouldReturn(['aggregated_pin_data' => ['id' => '56789']]);

        $provider->tried('12345')->toArray();

        $request = [
            'field_set_key'    => 'did_it',
            'show_did_it_feed' => true,
            'aggregated_pin_data_id' => '56789'
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_ACTIVITY, $request);
    }

    /**
     * @return string
     */
    protected function getProviderClass()
    {
        return Pins::class;
    }
}
