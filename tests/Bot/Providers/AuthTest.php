<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Auth;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class AuthTest
 * @method Auth getProvider()
 */
class AuthTest extends ProviderBaseTest
{
    /** @test */
    public function it_converts_simple_account_to_a_business_one()
    {
        $provider = $this->getProvider();

        $provider->convertToBusiness('myBusinessName', 'http://example.com');

        $request = [
            'business_name' => 'myBusinessName',
            'website_url'   => 'http://example.com',
            'account_type'  => 'other',
        ];

        $this->assertWasPostRequest(UrlBuilder::RESOURCE_CONVERT_TO_BUSINESS, $request);
    }

    /** @test */
    public function it_confirms_emails()
    {
        $provider = $this->getProvider();
        $provider->confirmEmail('http://some-link-form-email.com');

        $this->assertWasGetRequest('http://some-link-form-email.com');
    }

    /** @test */
    public function it_delegates_logout_to_request_object()
    {
        $provider = $this->getProvider();

        $provider->logout();
        $this->assertFalse($this->request->isLoggedIn());
    }

    protected function getProviderClass()
    {
        return Auth::class;
    }
}
