<?php

namespace seregazhuk\tests\Api;

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
    public function sendMessage()
    {
        $response = $this->createMessageSendResponse();

        $userId = '0000000000000';
        $message = 'test';

        $this->setResponse($response);        
        $this->assertTrue($this->provider->sendMessage($userId, $message));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->sendMessage($userId, $message));
    }

    /** @test */
    public function sendEmail()
    {
        $response = $this->createMessageSendResponse();
        $email = 'test@email.com';
        $message = 'test';

        $this->setResponse($response);        
        $this->assertTrue($this->provider->sendEmail($email, $message));

        $this->setResponse($this->createErrorApiResponse());
        $this->assertFalse($this->provider->sendEmail($email, $message));
    }

    /**
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\InvalidRequestException
     */
    public function failWhenEmptyUsersProvided()
    {
        $this->provider->sendMessage([], '');
    }

    /**
     * @test
     * @expectedException seregazhuk\PinterestBot\Exceptions\InvalidRequestException
     */
    public function failWhenEmptyEmailsProvided()
    {
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

        $this->setResponse($res);
        $this->assertEquals($lastConversations, $this->provider->last());
        
        $this->setResponse(null);
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
