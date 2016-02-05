<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

class User extends Provider {

    protected $loginRequired = ['profile'];

    public function profile($userInfo)
    {
        return $this->callPostRequest(
            $userInfo, UrlHelper::RESOURCE_UPDATE_USER_SETTINGS
        );
    }

}
