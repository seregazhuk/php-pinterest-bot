<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Conversations;
use seregazhuk\PinterestBot\Exceptions\InvalidRequestException;

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
     * @var
     */
    protected $providerClass = Conversations::class;

    /** @test */
    public function sendMessage()
    {
        $response = $this->createMessageSendResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturnNull();

        $userId = '0000000000000';
        $message = 'test';
        $this->assertTrue($this->provider->sendMessage($userId, $message));
        $this->assertFalse($this->provider->sendMessage($userId, $message));
    }

    /** @test */
    public function sendEmail()
    {
        $response = $this->createMessageSendResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturnNull();

        $email = 'test@email.com';
        $message = 'test';
        $this->assertTrue($this->provider->sendEmail($email, $message));
        $this->assertFalse($this->provider->sendEmail($email, $message));
    }

    /** @test */
    public function failWhenEmptyUsersProvided()
    {
        $this->expectException(InvalidRequestException::class);
        $this->provider->sendMessage([], '');
    }

    /** @test */
    public function failWhenEmptyEmailsProvided()
    {
        $this->expectException(InvalidRequestException::class);
        $this->provider->sendEmail([], '');
    }

    /** @test */
    public function getLastConversation()
    {
        $lastConversations = [
            '1' => ['result'],
        ];

        $res = $this->createApiResponse(
            [
                'data'  => $lastConversations,
                'error' => null,
            ]
        );

        $this->mock->shouldReceive('exec')->once()->andReturn($res);
        $this->mock->shouldReceive('exec')->once()->andReturnNull();

        $this->assertEquals($lastConversations, $this->provider->last());
        $this->assertFalse($this->provider->last());
    }

    /**
     * @return array
     */
    protected function createMessageSendResponse()
    {
        $data = [
            'data'  => ['id' => '0000000000000'],
            'error' => null,
        ];

        return $this->createApiResponse($data);
    }
}
