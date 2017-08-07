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
            'id'            => '12345',
            'field_set_key' => 'detailed',
        ];
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_PIN_INFO, $request);
    }

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
