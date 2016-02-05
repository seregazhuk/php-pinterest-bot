<?php

namespace seregazhuk\PinterestBot\Api\Providers;

use seregazhuk\PinterestBot\Helpers\UrlHelper;

class User extends Provider {

    public function profile($userInfo)
    {
        return $this->callPostRequest($userInfo, UrlHelper::RESOURCE_UPDATE_USER_SETTINGS,true);
    }

}
