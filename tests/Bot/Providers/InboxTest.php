<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Inbox;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class InboxTest
 * @method Inbox getProvider()
 */
class InboxTest extends BaseProviderTest
{
    /** @test */
    public function it_returns_conversations_for_a_current_user()
    {
        $provider = $this->getProvider();
        $provider->conversations();
        
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_LAST_CONVERSATIONS);
    }

    /** @test */
    public function it_returns_current_contact_requests()
    {
        $provider = $this->getProvider();
        $requests = $provider->contactRequests();

        $this->assertInternalType('array', $requests);
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_CONTACTS_REQUESTS);
    }


    
    protected function getProviderClass()
    {
        return Inbox::class;
    }
}
