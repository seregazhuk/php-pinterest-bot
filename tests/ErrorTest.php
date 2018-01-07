<?php

use PHPUnit\Framework\TestCase;
use seregazhuk\PinterestBot\Api\Error;

class ErrorTest extends TestCase
{
    /** @test */
    public function it_should_return_last_message_from_response()
    {
        $errorData = [
            'message' => null,
            'code'    => 'error_code',
        ];

        $error = new Error($errorData);

        $this->assertEquals($errorData['code'], $error->getText());
    }

    /** @test */
    public function it_should_return_null_if_there_was_no_error_in_response()
    {
        $error = new Error();

        $this->assertNull($error->getText());
    }

    /** @test */
    public function it_should_return_last_error_code_from_response()
    {
        $errorData = [
            'message' => 'error_message',
            'code'    => 'error_code',
        ];

        $error = new Error($errorData);

        $this->assertEquals($errorData['message'], $error->getText());
    }
}
