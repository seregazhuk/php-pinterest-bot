<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Api\Providers\Conversations;

/**
 * Class ConversationsTest.
 */
class ConversationsTest extends ProviderTest
{
    /**
     * @var Conversations
     */
    protected $provider;

    /**
     * @var string
     */
    protected $providerClass = Conversations::class;

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
    public function it_should_return_last_conversation()
    {
        $lastConversations = [
            '1' => ['result'],
        ];

        $this->apiShouldReturnData($lastConversations)
            ->assertEquals($lastConversations, $this->provider->last());
        
        $this->apiShouldReturnEmpty()
            ->assertFalse($this->provider->last());
    }

    /**
     * @return $this
     */
    protected function apiShouldSendMessage()
    {
        return $this->apiShouldReturnData(['id' => '1']);
    }
}
