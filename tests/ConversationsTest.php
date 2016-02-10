<?php
namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\Conversations;

class ConversationsTest extends ProviderTest
{
    /**
     * @var Conversations
     */
    protected $provider;
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
    public function getLastConversation()
    {
        $lastConversations = array(
            "1" => ["result"],
        );

        $res = $this->createApiResponse(
            array(
                'data' => $lastConversations,
                'error' => null,
            )
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
        $data = array(
            'data'  => array("id" => "0000000000000"),
            'error' => null,
        );
        return $this->createApiResponse($data);
    }
}
