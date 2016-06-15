<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;
use seregazhuk\PinterestBot\Api\Traits\UploadsImages;

class User extends Provider
{
    use UploadsImages;

    protected $loginRequiredFor = ['profile'];

    /**
     * Update user profile info. Gets associative array as a param. Available keys of array are:
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     * You can also change user avatar by passing 'profile_image'.
     *
     * @param array $userInfo
     *
     * @return mixed
     */
    public function profile($userInfo)
    {
        if (isset($userInfo['profile_image'])) {
            $userInfo['profile_image_url'] = $this->upload($userInfo['profile_image']);
        }

        return $this->execPostRequest($userInfo, UrlHelper::RESOURCE_UPDATE_USER_SETTINGS);
    }
}
