<?php

namespace seregazhuk\tests\Api;

use seregazhuk\PinterestBot\Api\Providers\User;
use seregazhuk\PinterestBot\Helpers\UrlHelper;

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
        $attributes = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($attributes));

        $this->setErrorResponse();
        $this->assertFalse($this->provider->profile($attributes));
    }

    /** @test */
    public function editProfileWithImage()
    {
        $attributes = [
            'name'          => 'John Doe',
            'profile_image' => 'my_profile_image.jpg'
        ];
        $this->requestMock->shouldReceive('upload')->withArgs(
                [
                    $attributes['profile_image'],
                    UrlHelper::IMAGE_UPLOAD
                ]
            );
        $this->setSuccessResponse();
        $this->assertTrue($this->provider->profile($attributes));
    }
}
