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

    public function testSendMessage()
    {
        $response = $this->createMessageSendResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(3))->method('exec')->willReturn(null);

        $userId = '0000000000000';
        $message = 'test';
        $this->assertTrue($this->provider->sendMessage($userId, $message));
        $this->assertFalse($this->provider->sendMessage($userId, $message));
    }

    public function testLast()
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

        $this->mock->expects($this->at(1))->method('exec')->willReturn($res);
        $this->mock->expects($this->at(3))->method('exec')->willReturn(null);

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
