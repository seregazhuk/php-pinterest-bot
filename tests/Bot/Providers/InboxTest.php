<?php

namespace seregazhuk\tests\Bot\Providers;

use seregazhuk\PinterestBot\Api\Providers\Inbox;
use seregazhuk\PinterestBot\Helpers\UrlBuilder;

/**
 * Class InboxTest
 * @method Inbox getProvider()
 */
class InboxTest extends ProviderBaseTest
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

    /** @test */
    public function it_accepts_contact_request()
    {
        $provider = $this->getProvider();
        $provider->acceptContactRequest('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CONTACT_REQUEST_ACCEPT,
            $this->createContactRequestData('12345')
        );
    }

    /** @test */
    public function it_ignores_contact_request()
    {
        $provider = $this->getProvider();
        $provider->ignoreContactRequest('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CONTACT_REQUEST_IGNORE,
            $this->createContactRequestData('12345')
        );
    }

    /** @test */
    public function it_fetches_current_user_news()
    {
        $provider = $this->getProvider();
        $provider->news()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_LATEST_NEWS, ['allow_stale' => true]);
    }

    /** @test */
    public function it_fetches_current_user_notifications()
    {
        $provider = $this->getProvider();
        $provider->notifications()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_NOTIFICATIONS);
    }

    /**
     * @param string $requestId
     * @return array
     */
    protected function createContactRequestData($requestId)
    {
        return [
            'contact_request' => [
                "type" => "contactrequest",
                "id"   => $requestId,
            ],
        ];
    }

    protected function getProviderClass()
    {
        return Inbox::class;
    }
}
