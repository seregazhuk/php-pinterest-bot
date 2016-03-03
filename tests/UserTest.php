<?php

namespace seregazhuk\tests;

use seregazhuk\PinterestBot\Api\Providers\User;

/**
 * Class UserTest.
 */
class UserTest extends ProviderTest
{
    /**
     * @var User
     */
    protected $provider;
    protected $providerClass = User::class;

    /** @test */
    public function editProfile()
    {
        $response = $this->createSuccessApiResponse();
        $error = $this->createErrorApiResponse();

        $this->mock->shouldReceive('exec')->once()->andReturn($response);
        $this->mock->shouldReceive('exec')->once()->andReturn($error);

        $params = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($params));
        $this->assertFalse($this->provider->profile($params));
    }
}
