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
        $this->login();

        $this->getProvider()->conversations()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_LAST_CONVERSATIONS);
    }

    /** @test */
    public function it_returns_messages_for_a_specified_conversation()
    {
        $this->login();

        $this->getProvider()->getMessages('123')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_CONVERSATION_MESSAGES, ['conversation_id' => '123']);
    }

    /** @test */
    public function it_returns_current_contact_requests()
    {
        $this->login();

        $requests = $this->getProvider()->contactRequests();

        $this->assertInternalType('array', $requests);
        $this->assertWasGetRequest(UrlBuilder::RESOURCE_CONTACTS_REQUESTS);
    }

    /** @test */
    public function it_accepts_contact_request()
    {
        $this->login();

        $this->getProvider()->acceptContactRequest('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CONTACT_REQUEST_ACCEPT,
            $this->createContactRequestData('12345')
        );
    }

    /** @test */
    public function it_ignores_contact_request()
    {
        $this->login();

        $this->getProvider()->ignoreContactRequest('12345');

        $this->assertWasPostRequest(
            UrlBuilder::RESOURCE_CONTACT_REQUEST_IGNORE,
            $this->createContactRequestData('12345')
        );
    }

    /** @test */
    public function it_fetches_current_user_news()
    {
        $this->login();

        $this->getProvider()->news()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_LATEST_NEWS, ['allow_stale' => true]);
    }

    /** @test */
    public function it_fetches_current_user_notifications()
    {
        $this->login();

        $this->getProvider()->notifications()->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_NOTIFICATIONS);
    }


    /** @test */
    public function it_fetches_details_for_a_specified_notification()
    {
        $this->login();

        $this->getProvider()->newsHubDetails('12345')->toArray();

        $this->assertWasGetRequest(UrlBuilder::RESOURCE_GET_NEWS_HUB_DETAILS, ['news_id' => '12345']);
    }

    /**
     * @param string $requestId
     * @return array
     */
    protected function createContactRequestData($requestId)
    {
        return [
            'contact_request' => [
                'type' => 'contactrequest',
                'id'   => $requestId,
            ],
        ];
    }

    protected function getProviderClass()
    {
        return Inbox::class;
    }
}
