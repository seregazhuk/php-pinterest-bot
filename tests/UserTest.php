<?php 

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\User;

/**
 * Class UserTest
 * @package seregazhuk\tests
 */ 
class UserTest extends ProviderTest
{
    /**
     * @var Interests
     */
    protected $provider;
    protected $providerClass = User::class;


    /** @test */
    public function profile()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->expects($this->at(1))->method('exec')->willReturn($response);
        $this->mock->expects($this->at(1))->method('exec')->willReturn($error);

        $params = ['name'=>'name'];
        $this->assertTrue($this->provider->profile($params));
        $this->assertFalse($this->provider->profile($params));
    }
}

