<?php
namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Providers\Conversations;

class ConversationsTest extends ProviderTest
{
    /**
     * @var Conversations
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new Conversations($this->createRequestMock());
        parent::setUp();
    }

    public function testSendMessage()
    {
        $res = $this->createMessageSendResponse();

        $mock = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(2))->method('exec')->willReturn(null);
        $this->setProperty('request', $mock);

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

        $mock = $this->createRequestMock();
        $mock->expects($this->at(1))->method('exec')->willReturn($res);
        $mock->expects($this->at(2))->method('exec')->willReturn(null);
        $this->setProperty('request', $mock);

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
