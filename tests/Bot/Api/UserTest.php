<?php

namespace seregazhuk\tests\Bot\Api;

use seregazhuk\PinterestBot\Helpers\UrlBuilder;
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

    /**
     * @var string
     */
    protected $providerClass = User::class;

    /** @test */
    public function it_should_edit_user_profile()
    {
        $this->apiShouldReturnSuccess();
        $attributes = ['name' => 'name'];
        $this->assertTrue($this->provider->profile($attributes));

        $this->apiShouldReturnError()
            ->assertFalse($this->provider->profile($attributes));
    }

    /** @test */
    public function it_should_return_current_user_profile()
    {
        $profile = ['username' => 'test'];

        $this->apiShouldReturnData($profile)
            ->assertEquals($profile, $this->provider->profile());
    }

    /** @test */
    public function it_should_return_ban_info_from_profile()
    {
        $profile = ['is_write_banned' => true];

        $this->apiShouldReturnData($profile)
            ->assertEquals($profile['is_write_banned'], $this->provider->isBanned());
    }

    /** @test */
    public function it_should_return_username_from_profile()
    {
        $profile = ['username' => 'test'];

        $this->apiShouldReturnData($profile)
            ->assertEquals($profile['username'], $this->provider->getUserName());
    }

    /** @test */
    public function it_should_upload_image_when_editing_profile_with_local_image()
    {
        $attributes = [
            'name'          => 'John Doe',
            'profile_image' => 'my_profile_image.jpg'
        ];
        $this->request
            ->shouldReceive('upload')
            ->withArgs([$attributes['profile_image'], UrlBuilder::IMAGE_UPLOAD])
            ->andReturn(json_encode([
                'success' => true,
                'image_url' => 'http://example.com/example.jpg'
            ]));
        
        $this->apiShouldReturnSuccess()
            ->assertTrue($this->provider->profile($attributes));
    }
}
