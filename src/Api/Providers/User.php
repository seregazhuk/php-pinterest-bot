<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

class User extends Provider {

    protected $loginRequired = ['profile'];
    
    /**
     * Update user profile info. Gets associative array as
     * a params. Available keys of array are: 
     * 'last_name', 'first_name', 'username', 'about', 'location' and 'website_url'.
     *
     * @param array $userInfo
     */ 
    public function profile($userInfo)
    {
        return $this->callPostRequest(
            $userInfo, UrlHelper::RESOURCE_UPDATE_USER_SETTINGS
        );
    }

}
