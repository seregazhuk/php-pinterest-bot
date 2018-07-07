<?php

namespace seregazhuk\tests\Bot;

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;

/**
 * Class RequestTest.
 */
class SendsMessagesTest extends TestCase
{
    /** @test */
    public function it_doesnt_allow_to_send_messages_without_specifying_emails_or_users()
    {
        $this->expectException(InvalidRequest::class);
        /** @var SendsMessages $object */
        $object = $this->getMockForTrait(SendsMessages::class);

        $object
            ->expects($this->any())
             ->method('post');

        $object->send(1, 'message', [], []);
    }
}
