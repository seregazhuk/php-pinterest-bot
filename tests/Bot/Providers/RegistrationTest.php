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

    protected function getProviderClass()
    {
        return Auth::class;
    }
}
