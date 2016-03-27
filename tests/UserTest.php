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
        $this->setSuccessResponse();
        $params = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($params));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->profile($params));
    }
}
