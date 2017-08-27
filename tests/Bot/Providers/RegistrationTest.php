<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Forms\Registration;
use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class RegistrationTest
 * @method Auth getProvider()
 */
class RegistrationTest extends ProviderBaseTest
{
    /** @test */
    public function it_creates_a_form_from_plain_params()
    {
        $provider = $this->getProvider();
        $provider->register('johndoe@example.com', 'secret', 'johnDoe');

        $form = new Registration('johndoe@example.com', 'secret', 'johnDoe');
        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CREATE_REGISTER, $form->toArray());
    }

    /** @test */
    public function it_converts_account_to_business_type_when_registering_a_business_account()
    {
        $provider = $this->getProvider();
        $provider->registerBusiness('johndoe@example.com', 'secret', 'johnDoe');

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS, [
            'business_name' => 'johnDoe',
            'website_url'   => '',
            'account_type'  => 'other',
        ]);
    }

    protected function getProviderClass()
    {
        return Auth::class;
    }
}
