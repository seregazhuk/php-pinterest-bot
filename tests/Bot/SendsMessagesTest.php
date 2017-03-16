<?php

namespace seregazhuk\tests\Bot;

use Mockery;
use ReflectionClass;
use Mockery\MockInterface;
use PHPUnit_Framework_TestCase;
use seregazhuk\PinterestBot\Api\Request;
use seregazhuk\PinterestBot\Api\Traits\SendsMessages;
use seregazhuk\PinterestBot\Exceptions\InvalidRequest;
use seregazhuk\tests\Helpers\CookiesHelper;
use seregazhuk\PinterestBot\Helpers\Cookies;
use seregazhuk\tests\Helpers\ResponseHelper;
use seregazhuk\tests\Helpers\ReflectionHelper;
use seregazhuk\PinterestBot\Api\CurlHttpClient;
use seregazhuk\PinterestBot\Api\Contracts\HttpClient;

/**
 * Class RequestTest.
 */
class SendsMessagesTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_doesnt_allow_to_send_messages_without_specifying_emails_or_users()
    {
        $this->setExpectedException(InvalidRequest::class);
        /** @var SendsMessages $object */
        $object = $this->getMockForTrait(SendsMessages::class);
        $object->expects($this->any())
             ->method('post');

        $object->send(1, 'message', [], []);
    }
}
