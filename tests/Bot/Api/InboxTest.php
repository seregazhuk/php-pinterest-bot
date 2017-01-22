<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\Providers\Inbox;

/**
 * Class InboxTest.
 */
class InboxTest extends ProviderTest
{
    /**
     * @var Inbox
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Inbox::class;

    /** @test */
    public function it_should_send_messages()
    {
        $userId = '1';
        $message = 'test';

        $this->apiShouldSendMessage()
            ->assertTrue($this->provider->sendMessage($userId, $message));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->sendMessage($userId, $message));
    }

    /** @test */
    public function it_should_send_emails()
    {
        $email = 'test@email.com';
        $message = 'test';

        $this->apiShouldSendMessage()
            ->assertTrue($this->provider->sendEmail($email, $message));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->sendEmail($email, $message));
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\InvalidRequest
     */
    public function it_should_throw_exception_when_sending_message_to_no_users()
    {
        $this->provider->sendMessage([], '');
    }

    /**
     * @test
     * @expectedException \seregazhuk\PinterestBot\Exceptions\InvalidRequest
     */
    public function it_should_throw_exception_when_sending_email_to_no_emails()
    {
        $this->provider->sendEmail([], '');
    }

    /** @test */
    public function it_should_return_conversations()
    {
        $lastInbox = [
            '1' => ['result'],
        ];

        $this->apiShouldReturnData($lastInbox)
            ->assertEquals($lastInbox, $this->provider->conversations());
        
        $this->apiShouldReturnEmpty()
            ->assertFalse($this->provider->conversations());
    }

    /** @test */
    public function it_returns_users_news()
    {
        $this->apiShouldReturnPagination($this->paginatedResponse)
            ->assertIsPaginatedResponse($news = $this->provider->news())
            ->assertPaginatedResponseEquals($this->paginatedResponse, $news);
    }

    /**
     * @return $this
     */
    protected function apiShouldSendMessage()
    {
        return $this->apiShouldReturnData(['id' => '1']);
    }
}
